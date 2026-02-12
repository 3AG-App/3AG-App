import { Head, router } from '@inertiajs/react';
import { AlertTriangle, Bell, Calendar, Check, Key, Languages, Monitor, Moon, Palette, Shield, Sun, Trash2 } from 'lucide-react';
import { useTheme } from 'next-themes';
import { useState } from 'react';

import { update as updateSettings } from '@/actions/App/Http/Controllers/Dashboard/SettingsController';
import { update as updateLocale } from '@/actions/App/Http/Controllers/LocaleController';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { useTranslations } from '@/hooks/use-translations';
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

const localeLabels: Record<string, string> = {
    en: 'English',
    de: 'Deutsch',
    fr: 'Français',
};

function getLocaleLabel(locale: string): string {
    return localeLabels[locale] ?? locale.toUpperCase();
}

function ThemeButton({
    theme,
    currentTheme,
    onClick,
    icon: Icon,
    label,
}: {
    theme: string;
    currentTheme: string | undefined;
    onClick: () => void;
    icon: React.ElementType;
    label: string;
}) {
    const isActive = currentTheme === theme;
    return (
        <button
            onClick={onClick}
            className={`flex flex-1 flex-col items-center gap-2 rounded-lg border-2 p-4 transition-all ${
                isActive
                    ? 'border-primary bg-primary/5 text-primary'
                    : 'border-muted bg-muted/30 text-muted-foreground hover:border-muted-foreground/50 hover:bg-muted/50'
            }`}
        >
            <Icon className="h-6 w-6" />
            <span className="text-sm font-medium">{label}</span>
            {isActive && <Check className="h-4 w-4" />}
        </button>
    );
}

function NotificationToggle({
    icon: Icon,
    title,
    description,
    checked,
    onChange,
}: {
    icon: React.ElementType;
    title: string;
    description: string;
    checked: boolean;
    onChange: (checked: boolean) => void;
}) {
    return (
        <div
            className={`flex items-center justify-between rounded-lg border p-4 transition-all ${
                checked ? 'border-primary/20 bg-primary/5' : 'border-muted bg-muted/30'
            }`}
        >
            <div className="flex items-start gap-3">
                <div
                    className={`flex h-10 w-10 shrink-0 items-center justify-center rounded-lg ${
                        checked ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground'
                    }`}
                >
                    <Icon className="h-5 w-5" />
                </div>
                <div>
                    <p className="font-medium">{title}</p>
                    <p className="text-sm text-muted-foreground">{description}</p>
                </div>
            </div>
            <Switch checked={checked} onCheckedChange={onChange} />
        </div>
    );
}

export default function Settings({ preference }: SettingsProps) {
    const { t, locale, supportedLocales } = useTranslations();
    const { theme, setTheme } = useTheme();
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [deleteConfirmation, setDeleteConfirmation] = useState('');
    const [isChangingLocale, setIsChangingLocale] = useState(false);

    const updatePreference = (key: keyof Preference, value: boolean | string) => {
        router.put(updateSettings.url(), { [key]: value }, { preserveScroll: true });
    };

    const changeLocale = (nextLocale: string) => {
        if (!nextLocale || nextLocale === locale) {
            return;
        }

        router.post(
            updateLocale.url(),
            { locale: nextLocale },
            {
                preserveScroll: true,
                onStart: () => setIsChangingLocale(true),
                onFinish: () => setIsChangingLocale(false),
            },
        );
    };

    return (
        <DashboardLayout breadcrumbs={[{ label: t('dashboard.nav.settings', 'Settings') }]}>
            <Head title={t('dashboard.nav.settings', 'Settings')} />

            <div className="space-y-6">
                {/* Page Header */}
                <div>
                    <h1 className="text-3xl font-bold tracking-tight">{t('dashboard.settings.title', 'Settings')}</h1>
                    <p className="text-muted-foreground">{t('dashboard.settings.subtitle', 'Manage your account preferences and settings.')}</p>
                </div>

                {/* Appearance */}
                <Card>
                    <CardHeader className="pb-4">
                        <CardTitle className="flex items-center gap-2 text-lg">
                            <Palette className="h-5 w-5" />
                            {t('dashboard.settings.appearance.title', 'Appearance')}
                        </CardTitle>
                        <CardDescription>
                            {t('dashboard.settings.appearance.description', 'Customize the appearance of the application.')}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div>
                            <Label className="mb-3 block text-sm font-medium">{t('dashboard.settings.appearance.theme', 'Theme')}</Label>
                            <div className="flex gap-3">
                                <ThemeButton
                                    theme="light"
                                    currentTheme={theme}
                                    onClick={() => setTheme('light')}
                                    icon={Sun}
                                    label={t('dashboard.settings.appearance.light', 'Light')}
                                />
                                <ThemeButton
                                    theme="dark"
                                    currentTheme={theme}
                                    onClick={() => setTheme('dark')}
                                    icon={Moon}
                                    label={t('dashboard.settings.appearance.dark', 'Dark')}
                                />
                                <ThemeButton
                                    theme="system"
                                    currentTheme={theme}
                                    onClick={() => setTheme('system')}
                                    icon={Monitor}
                                    label={t('dashboard.settings.appearance.system', 'System')}
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Language */}
                <Card>
                    <CardHeader className="pb-4">
                        <CardTitle className="flex items-center gap-2 text-lg">
                            <Languages className="h-5 w-5" />
                            {t('dashboard.settings.language.title', 'Language')}
                        </CardTitle>
                        <CardDescription>
                            {t('dashboard.settings.language.description', 'Choose the language you want to use in your dashboard.')}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-2">
                            <Label htmlFor="locale" className="text-sm font-medium">
                                {t('dashboard.settings.language.label', 'Language')}
                            </Label>
                            <Select value={locale} onValueChange={changeLocale} disabled={isChangingLocale}>
                                <SelectTrigger id="locale" className="w-full sm:w-[260px]">
                                    <SelectValue placeholder={t('dashboard.settings.language.placeholder', 'Select language')} />
                                </SelectTrigger>
                                <SelectContent align="start">
                                    {(supportedLocales ?? []).map((supportedLocale) => (
                                        <SelectItem key={supportedLocale} value={supportedLocale}>
                                            {getLocaleLabel(supportedLocale)}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <p className="text-xs text-muted-foreground">
                                {t(
                                    'dashboard.settings.language.help',
                                    'This will update your language preference for your account and this browser.',
                                )}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                {/* Notifications */}
                <Card>
                    <CardHeader className="pb-4">
                        <CardTitle className="flex items-center gap-2 text-lg">
                            <Bell className="h-5 w-5" />
                            {t('dashboard.settings.notifications.title', 'Notifications')}
                        </CardTitle>
                        <CardDescription>
                            {t('dashboard.settings.notifications.description', 'Configure how you receive notifications.')}
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <NotificationToggle
                            icon={Bell}
                            title={t('dashboard.settings.notifications.emailTitle', 'Email Notifications')}
                            description={t(
                                'dashboard.settings.notifications.emailDescription',
                                'Receive email notifications about your account activity.',
                            )}
                            checked={preference.notifications_enabled}
                            onChange={(enabled) => updatePreference('notifications_enabled', enabled)}
                        />
                        <NotificationToggle
                            icon={Calendar}
                            title={t('dashboard.settings.notifications.subscriptionTitle', 'Subscription Reminders')}
                            description={t(
                                'dashboard.settings.notifications.subscriptionDescription',
                                'Get notified before your subscriptions renew.',
                            )}
                            checked={preference.subscription_reminders}
                            onChange={(enabled) => updatePreference('subscription_reminders', enabled)}
                        />
                        <NotificationToggle
                            icon={Key}
                            title={t('dashboard.settings.notifications.licenseTitle', 'License Expiry Alerts')}
                            description={t(
                                'dashboard.settings.notifications.licenseDescription',
                                'Receive alerts when your licenses are about to expire.',
                            )}
                            checked={preference.license_expiry_alerts}
                            onChange={(enabled) => updatePreference('license_expiry_alerts', enabled)}
                        />
                    </CardContent>
                    <CardFooter className="border-t bg-muted/30 px-6 py-3">
                        <p className="text-xs text-muted-foreground">
                            {t(
                                'dashboard.settings.notifications.footer',
                                'Notifications are sent to your registered email address. You can update your email in your profile settings.',
                            )}
                        </p>
                    </CardFooter>
                </Card>

                {/* Security */}
                <Card>
                    <CardHeader className="pb-4">
                        <CardTitle className="flex items-center gap-2 text-lg">
                            <Shield className="h-5 w-5" />
                            {t('dashboard.settings.security.title', 'Security')}
                        </CardTitle>
                        <CardDescription>{t('dashboard.settings.security.description', 'Manage your security settings.')}</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="rounded-lg border bg-muted/30 p-4">
                            <div className="flex items-start gap-3">
                                <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    <Check className="h-5 w-5" />
                                </div>
                                <div>
                                    <p className="font-medium">{t('dashboard.settings.security.passwordProtected', 'Password Protected')}</p>
                                    <p className="text-sm text-muted-foreground">
                                        {t(
                                            'dashboard.settings.security.passwordProtectedDescription',
                                            'Your account is secured with a password. You can change it in your profile settings.',
                                        )}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Danger Zone */}
                <Card className="border-destructive/50">
                    <CardHeader className="pb-4">
                        <CardTitle className="flex items-center gap-2 text-lg text-destructive">
                            <AlertTriangle className="h-5 w-5" />
                            {t('dashboard.settings.danger.title', 'Danger Zone')}
                        </CardTitle>
                        <CardDescription>{t('dashboard.settings.danger.description', 'Irreversible and destructive actions.')}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="flex flex-col gap-4 rounded-lg border border-destructive/50 bg-destructive/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div className="flex items-start gap-3">
                                <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-destructive/10 text-destructive">
                                    <Trash2 className="h-5 w-5" />
                                </div>
                                <div>
                                    <h4 className="font-medium">{t('dashboard.settings.danger.deleteTitle', 'Delete Account')}</h4>
                                    <p className="text-sm text-muted-foreground">
                                        {t(
                                            'dashboard.settings.danger.deleteDescription',
                                            'Permanently delete your account and all associated data. This action cannot be undone.',
                                        )}
                                    </p>
                                </div>
                            </div>
                            <Button variant="destructive" onClick={() => setDeleteDialogOpen(true)} className="shrink-0">
                                <Trash2 className="mr-2 h-4 w-4" />
                                {t('dashboard.settings.danger.deleteButton', 'Delete Account')}
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            {/* Delete Account Dialog */}
            <AlertDialog open={deleteDialogOpen} onOpenChange={setDeleteDialogOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle className="flex items-center gap-2 text-destructive">
                            <AlertTriangle className="h-5 w-5" />
                            {t('dashboard.settings.danger.dialogTitle', 'Delete Account?')}
                        </AlertDialogTitle>
                        <AlertDialogDescription className="space-y-4">
                            <p>
                                {t(
                                    'dashboard.settings.danger.dialogDescription',
                                    'This action cannot be undone. This will permanently delete your account and remove all associated data including:',
                                )}
                            </p>
                            <ul className="list-inside list-disc space-y-1 text-sm">
                                <li>{t('dashboard.settings.danger.bulletLicenses', 'All your licenses and activations')}</li>
                                <li>{t('dashboard.settings.danger.bulletSubscriptions', 'Your subscription history')}</li>
                                <li>{t('dashboard.settings.danger.bulletProfile', 'Your profile information')}</li>
                            </ul>
                            <div className="space-y-2">
                                <Label htmlFor="delete-confirmation">{t('dashboard.settings.danger.typeDelete', 'Type "DELETE" to confirm')}</Label>
                                <Input
                                    id="delete-confirmation"
                                    value={deleteConfirmation}
                                    onChange={(e) => setDeleteConfirmation(e.target.value)}
                                    placeholder="DELETE"
                                />
                            </div>
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel onClick={() => setDeleteConfirmation('')}>{t('common.cancel', 'Cancel')}</AlertDialogCancel>
                        <AlertDialogAction
                            disabled={deleteConfirmation !== 'DELETE'}
                            onClick={() => {
                                router.delete('/dashboard/account');
                            }}
                            className="text-destructive-foreground bg-destructive hover:bg-destructive/90"
                        >
                            {t('dashboard.settings.danger.deleteMyAccount', 'Delete My Account')}
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </DashboardLayout>
    );
}

Settings.layout = null;
