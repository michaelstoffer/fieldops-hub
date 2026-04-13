import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
    roles: string[];
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SubscriptionData {
    status: string;
    plan: string;
    active_plan: string;
    is_trialing: boolean;
    days_remaining: number;
    trial_ends_at: string | null;
}

export interface PlanData {
    current: string;
    active: string;
    tech_limit: number | null;
    tech_count: number;
    at_tech_limit: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    subscription: SubscriptionData | null;
    plan: PlanData | null;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
