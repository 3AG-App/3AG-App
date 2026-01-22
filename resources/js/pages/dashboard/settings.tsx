import { Head, router } from '@inertiajs/react';
import { Bell, Moon, Palette, Sun } from 'lucide-react';
import { useTheme } from 'next-themes';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Switch } from '@/components/ui/switch';
import DashboardLayout from '@/layouts/dashboard-layout';
import type { User } from '@/types';

interface Preference {
    notifications_enabled: boolean;
    subscription_reminders: boolean;
    license_expiry_alerts: boolean;
    timezone: string | null;
}

interface SettingsProps {
    user: User;
    preference: Preference;
}

export default function Settings({ preference }: SettingsProps) {
    const { theme, setTheme } = useTheme();

    const updatePreference = (key: keyof Preference, value: boolean | string) => {
        router.put('/dashboard/settings', { [key]: value }, { preserveScroll: true });
    };

    return (
        <DashboardLayout breadcrumbs={[{ label: 'Settings' }]}>
            <Head title="Settings" />

            <div className="space-y-8">
                {/* Page Header */}
                <div>
                    <h1 className="text-3xl font-bold tracking-tight">Settings</h1>
                    <p className="text-muted-foreground">Manage your account preferences and settings.</p>
                </div>

                {/* Appearance */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Palette className="h-5 w-5" />
                            Appearance
                        </CardTitle>
                        <CardDescription>Customize the appearance of the application.</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        <div>
                            <Label className="mb-3 block text-sm font-medium">Theme</Label>
                            <div className="flex gap-3">
                                <Button variant={theme === 'light' ? 'default' : 'outline'} className="flex-1" onClick={() => setTheme('light')}>
                                    <Sun className="mr-2 h-4 w-4" />
                                    Light
                                </Button>
                                <Button variant={theme === 'dark' ? 'default' : 'outline'} className="flex-1" onClick={() => setTheme('dark')}>
                                    <Moon className="mr-2 h-4 w-4" />
                                    Dark
                                </Button>
                                <Button variant={theme === 'system' ? 'default' : 'outline'} className="flex-1" onClick={() => setTheme('system')}>
                                    System
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Notifications */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Bell className="h-5 w-5" />
                            Notifications
                        </CardTitle>
                        <CardDescription>Configure how you receive notifications.</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        <div className="flex items-center justify-between">
                            <div className="space-y-0.5">
                                <Label>Email Notifications</Label>
                                <p className="text-sm text-muted-foreground">Receive email notifications about your account activity.</p>
                            </div>
                            <Switch
                                checked={preference.notifications_enabled}
                                onCheckedChange={(enabled) => updatePreference('notifications_enabled', enabled)}
                            />
                        </div>

                        <Separator />

                        <div className="flex items-center justify-between">
                            <div className="space-y-0.5">
                                <Label>Subscription Reminders</Label>
                                <p className="text-sm text-muted-foreground">Get notified before your subscriptions renew.</p>
                            </div>
                            <Switch
                                checked={preference.subscription_reminders}
                                onCheckedChange={(enabled) => updatePreference('subscription_reminders', enabled)}
                            />
                        </div>

                        <div className="flex items-center justify-between">
                            <div className="space-y-0.5">
                                <Label>License Expiry Alerts</Label>
                                <p className="text-sm text-muted-foreground">Receive alerts when your licenses are about to expire.</p>
                            </div>
                            <Switch
                                checked={preference.license_expiry_alerts}
                                onCheckedChange={(enabled) => updatePreference('license_expiry_alerts', enabled)}
                            />
                        </div>
                    </CardContent>
                </Card>

                {/* Danger Zone */}
                <Card className="border-destructive/50">
                    <CardHeader>
                        <CardTitle className="text-destructive">Danger Zone</CardTitle>
                        <CardDescription>Irreversible and destructive actions.</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="flex items-center justify-between rounded-lg border border-destructive/50 p-4">
                            <div>
                                <h4 className="font-medium">Delete Account</h4>
                                <p className="text-sm text-muted-foreground">Permanently delete your account and all associated data.</p>
                            </div>
                            <Button variant="destructive" disabled>
                                Delete Account
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </DashboardLayout>
    );
}

Settings.layout = null;
