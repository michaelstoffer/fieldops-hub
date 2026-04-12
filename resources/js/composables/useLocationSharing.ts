import { onUnmounted, ref } from 'vue';

const STORAGE_KEY = 'locationSharingEnabled';
const INTERVAL_MS = 30_000; // 30 seconds

function getCsrfToken(): string {
    return (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null)?.content ?? '';
}

async function postLocation(position: GeolocationPosition): Promise<void> {
    await fetch('/api/technician/location', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': getCsrfToken(),
        },
        body: JSON.stringify({
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            heading: position.coords.heading,
            speed: position.coords.speed,
            recorded_at: new Date(position.timestamp).toISOString(),
        }),
    });
}

export function useLocationSharing() {
    const enabled = ref(localStorage.getItem(STORAGE_KEY) === 'true');
    const permissionDenied = ref(false);
    let intervalId: ReturnType<typeof setInterval> | null = null;

    function sendCurrentPosition() {
        navigator.geolocation.getCurrentPosition(
            (pos) => postLocation(pos),
            (err) => {
                if (err.code === GeolocationPositionError.PERMISSION_DENIED) {
                    permissionDenied.value = true;
                    stopSharing();
                }
            },
            { enableHighAccuracy: true, timeout: 10_000 },
        );
    }

    function startSharing() {
        if (!navigator.geolocation) return;
        if (intervalId !== null) return;

        sendCurrentPosition();
        intervalId = setInterval(sendCurrentPosition, INTERVAL_MS);
    }

    function stopSharing() {
        if (intervalId !== null) {
            clearInterval(intervalId);
            intervalId = null;
        }
    }

    async function toggle() {
        if (enabled.value) {
            enabled.value = false;
            localStorage.setItem(STORAGE_KEY, 'false');
            stopSharing();
            return;
        }

        if (!navigator.geolocation) {
            return;
        }

        // Request permission by attempting to get position
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                permissionDenied.value = false;
                enabled.value = true;
                localStorage.setItem(STORAGE_KEY, 'true');
                postLocation(pos);
                intervalId = setInterval(sendCurrentPosition, INTERVAL_MS);
            },
            (err) => {
                if (err.code === GeolocationPositionError.PERMISSION_DENIED) {
                    permissionDenied.value = true;
                }
            },
            { enableHighAccuracy: true, timeout: 10_000 },
        );
    }

    // Auto-start if previously enabled
    if (enabled.value && navigator.geolocation) {
        startSharing();
    }

    onUnmounted(() => stopSharing());

    return { enabled, permissionDenied, toggle };
}
