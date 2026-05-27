/**
 * Formats a US phone number string as (555) 555-5555 while the user types.
 * Strips all non-digits, then applies the mask up to 10 digits.
 * Returns both a handler for @input events and a standalone formatter function.
 */
export function usePhoneFormat() {
    function formatPhone(value: string): string {
        const digits = value.replace(/\D/g, '').slice(0, 10);
        if (digits.length === 0) return '';
        if (digits.length <= 3) return `(${digits}`;
        if (digits.length <= 6) return `(${digits.slice(0, 3)}) ${digits.slice(3)}`;
        return `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6)}`;
    }

    function onPhoneInput(event: Event, setter: (val: string) => void) {
        const input = event.target as HTMLInputElement;
        const formatted = formatPhone(input.value);
        setter(formatted);
        // Keep cursor at end after reformatting
        requestAnimationFrame(() => {
            input.value = formatted;
        });
    }

    return { formatPhone, onPhoneInput };
}
