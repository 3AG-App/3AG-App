import { Head, useForm } from '@inertiajs/react';
import { Calendar, Check, Key, Loader2, Lock, Mail, Shield, User } from 'lucide-react';
import { useMemo } from 'react';

import { update as updateProfile, updatePassword as updateProfilePassword } from '@/actions/App/Http/Controllers/Dashboard/ProfileController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Progress } from '@/components/ui/progress';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { useTranslations } from '@/hooks/use-translations';
import DashboardLayout from '@/layouts/dashboard-layout';
import type { User as UserType } from '@/types';

interface ProfileProps {
    user: UserType;
}

function getPasswordStrength(password: string, t: (key: string, fallback?: string) => string): { score: number; label: string; color: string } {
    let score = 0;
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;

    if (score <= 2) return { score: score * 20, label: t('dashboard.profile.passwordStrength.weak', 'Weak'), color: 'bg-red-500' };
    if (score <= 3) return { score: score * 20, label: t('dashboard.profile.passwordStrength.fair', 'Fair'), color: 'bg-amber-500' };
    if (score <= 4) return { score: score * 20, label: t('dashboard.profile.passwordStrength.good', 'Good'), color: 'bg-blue-500' };
    return { score: 100, label: t('dashboard.profile.passwordStrength.strong', 'Strong'), color: 'bg-green-500' };
}

function formatDate(dateString: string, locale: string): string {
    return new Date(dateString).toLocaleDateString(locale, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

export default function Profile({ user }: ProfileProps) {
    const { t, locale } = useTranslations();

    const profileForm = useForm({
        name: user.name,
        email: user.email,
    });

    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const passwordStrength = useMemo(() => getPasswordStrength(passwordForm.data.password, t), [passwordForm.data.password, t]);

    const handleProfileSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        profileForm.put(updateProfile.url(), {
            preserveScroll: true,
        });
    };

    const handlePasswordSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        passwordForm.put(updateProfilePassword.url(), {
            preserveScroll: true,
            onSuccess: () => {
                passwordForm.reset();
            },
        });
    };

    return (
        <DashboardLayout breadcrumbs={[{ label: t('dashboard.nav.profile', 'Profile') }]}>
            <Head title={t('dashboard.nav.profile', 'Profile')} />

            <div className="space-y-6">
                {/* Page Header */}
                <div>
                    <h1 className="text-3xl font-bold tracking-tight">{t('dashboard.profile.title', 'Profile')}</h1>
                    <p className="text-muted-foreground">{t('dashboard.profile.subtitle', 'Manage your account information and password.')}</p>
                </div>

                {/* Account Overview */}
                <Card className="bg-gradient-to-r from-primary/5 to-primary/10">
                    <CardContent className="flex flex-col gap-6 p-6 sm:flex-row sm:items-center">
                        <div className="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-primary/10">
                            <User className="h-10 w-10 text-primary" />
                        </div>
                        <div className="flex-1 space-y-1">
                            <h2 className="text-2xl font-bold">{user.name}</h2>
                            <p className="text-muted-foreground">{user.email}</p>
                            <div className="flex flex-wrap items-center gap-2 pt-2">
                                {user.email_verified_at ? (
                                    <Badge variant="default" className="gap-1">
                                        <Check className="h-3 w-3" />
                                        {t('dashboard.profile.emailVerified', 'Email Verified')}
                                    </Badge>
                                ) : (
                                    <Badge variant="secondary" className="gap-1">
                                        <Mail className="h-3 w-3" />
                                        {t('dashboard.profile.emailNotVerified', 'Email Not Verified')}
                                    </Badge>
                                )}
                                <Tooltip>
                                    <TooltipTrigger asChild>
                                        <Badge variant="outline" className="gap-1">
                                            <Calendar className="h-3 w-3" />
                                            {t('dashboard.profile.memberSince', 'Member since')} {formatDate(user.created_at, locale)}
                                        </Badge>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        {t('dashboard.profile.accountCreatedOn', 'Account created on')} {formatDate(user.created_at, locale)}
                                    </TooltipContent>
                                </Tooltip>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div className="grid gap-8 lg:grid-cols-2">
                    {/* Profile Information */}
                    <Card>
                        <CardHeader className="pb-4">
                            <CardTitle className="flex items-center gap-2 text-lg">
                                <User className="h-5 w-5" />
                                {t('dashboard.profile.profileInfo.title', 'Profile Information')}
                            </CardTitle>
                            <CardDescription>
                                {t('dashboard.profile.profileInfo.description', "Update your account's profile information and email address.")}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={handleProfileSubmit} className="space-y-4">
                                <div className="space-y-2">
                                    <Label htmlFor="name">{t('dashboard.profile.profileInfo.fullName', 'Full Name')}</Label>
                                    <div className="relative">
                                        <User className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                        <Input
                                            id="name"
                                            value={profileForm.data.name}
                                            onChange={(e) => profileForm.setData('name', e.target.value)}
                                            placeholder={t('dashboard.profile.profileInfo.namePlaceholder', 'Your name')}
                                            className="pl-10"
                                        />
                                    </div>
                                    {profileForm.errors.name && <p className="text-sm text-destructive">{profileForm.errors.name}</p>}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="email">{t('dashboard.profile.profileInfo.emailAddress', 'Email Address')}</Label>
                                    <div className="relative">
                                        <Mail className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                        <Input
                                            id="email"
                                            type="email"
                                            value={profileForm.data.email}
                                            onChange={(e) => profileForm.setData('email', e.target.value)}
                                            placeholder={t('dashboard.profile.profileInfo.emailPlaceholder', 'your@email.com')}
                                            className="pl-10"
                                        />
                                    </div>
                                    {profileForm.errors.email && <p className="text-sm text-destructive">{profileForm.errors.email}</p>}
                                </div>

                                <div className="flex items-center gap-4 pt-2">
                                    <Button type="submit" disabled={profileForm.processing}>
                                        {profileForm.processing && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                                        {t('common.saveChanges', 'Save Changes')}
                                    </Button>
                                    {profileForm.recentlySuccessful && (
                                        <span className="flex items-center gap-1 text-sm text-green-600">
                                            <Check className="h-4 w-4" />
                                            {t('common.saved', 'Saved!')}
                                        </span>
                                    )}
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    {/* Update Password */}
                    <Card>
                        <CardHeader className="pb-4">
                            <CardTitle className="flex items-center gap-2 text-lg">
                                <Key className="h-5 w-5" />
                                {t('dashboard.profile.password.title', 'Update Password')}
                            </CardTitle>
                            <CardDescription>
                                {t('dashboard.profile.password.description', 'Ensure your account is using a long, random password to stay secure.')}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={handlePasswordSubmit} className="space-y-4">
                                <div className="space-y-2">
                                    <Label htmlFor="current_password">{t('dashboard.profile.password.current', 'Current Password')}</Label>
                                    <div className="relative">
                                        <Lock className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                        <Input
                                            id="current_password"
                                            type="password"
                                            value={passwordForm.data.current_password}
                                            onChange={(e) => passwordForm.setData('current_password', e.target.value)}
                                            placeholder={t('dashboard.profile.password.currentPlaceholder', 'Enter current password')}
                                            className="pl-10"
                                        />
                                    </div>
                                    {passwordForm.errors.current_password && (
                                        <p className="text-sm text-destructive">{passwordForm.errors.current_password}</p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="password">{t('auth.newPassword', 'New Password')}</Label>
                                    <div className="relative">
                                        <Key className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                        <Input
                                            id="password"
                                            type="password"
                                            value={passwordForm.data.password}
                                            onChange={(e) => passwordForm.setData('password', e.target.value)}
                                            placeholder={t('dashboard.profile.password.newPlaceholder', 'Enter new password')}
                                            className="pl-10"
                                        />
                                    </div>
                                    {passwordForm.data.password && (
                                        <div className="space-y-1">
                                            <div className="flex items-center justify-between text-xs">
                                                <span className="text-muted-foreground">
                                                    {t('dashboard.profile.passwordStrength.label', 'Password Strength')}
                                                </span>
                                                <span
                                                    className={`font-medium ${passwordStrength.score >= 80 ? 'text-green-600' : passwordStrength.score >= 60 ? 'text-blue-600' : passwordStrength.score >= 40 ? 'text-amber-600' : 'text-red-600'}`}
                                                >
                                                    {passwordStrength.label}
                                                </span>
                                            </div>
                                            <Progress value={passwordStrength.score} className={`h-1.5 [&>div]:${passwordStrength.color}`} />
                                        </div>
                                    )}
                                    {passwordForm.errors.password && <p className="text-sm text-destructive">{passwordForm.errors.password}</p>}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="password_confirmation">{t('auth.confirmPassword', 'Confirm Password')}</Label>
                                    <div className="relative">
                                        <Shield className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                        <Input
                                            id="password_confirmation"
                                            type="password"
                                            value={passwordForm.data.password_confirmation}
                                            onChange={(e) => passwordForm.setData('password_confirmation', e.target.value)}
                                            placeholder={t('dashboard.profile.password.confirmPlaceholder', 'Confirm new password')}
                                            className="pl-10"
                                        />
                                    </div>
                                    {passwordForm.data.password && passwordForm.data.password_confirmation && (
                                        <p
                                            className={`text-xs ${passwordForm.data.password === passwordForm.data.password_confirmation ? 'text-green-600' : 'text-destructive'}`}
                                        >
                                            {passwordForm.data.password === passwordForm.data.password_confirmation
                                                ? t('dashboard.profile.password.match', '✓ Passwords match')
                                                : t('dashboard.profile.password.noMatch', '✗ Passwords do not match')}
                                        </p>
                                    )}
                                </div>

                                <div className="flex items-center gap-4 pt-2">
                                    <Button type="submit" disabled={passwordForm.processing}>
                                        {passwordForm.processing && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                                        {t('dashboard.profile.password.update', 'Update Password')}
                                    </Button>
                                    {passwordForm.recentlySuccessful && (
                                        <span className="flex items-center gap-1 text-sm text-green-600">
                                            <Check className="h-4 w-4" />
                                            {t('dashboard.profile.password.updated', 'Password updated!')}
                                        </span>
                                    )}
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>

                {/* Security Tips */}
                <Card className="border-blue-200 bg-blue-50/50 dark:border-blue-900 dark:bg-blue-950/50">
                    <CardContent className="flex items-start gap-4 p-4">
                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/50">
                            <Shield className="h-5 w-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h3 className="font-medium text-blue-900 dark:text-blue-100">
                                {t('dashboard.profile.securityTips.title', 'Security Tips')}
                            </h3>
                            <ul className="mt-1 space-y-1 text-sm text-blue-800 dark:text-blue-200">
                                <li>• {t('dashboard.profile.securityTips.tip1', "Use a unique password that you don't use anywhere else")}</li>
                                <li>
                                    • {t('dashboard.profile.securityTips.tip2', 'Include numbers, symbols, and both uppercase and lowercase letters')}
                                </li>
                                <li>• {t('dashboard.profile.securityTips.tip3', 'Consider using a password manager for better security')}</li>
                            </ul>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </DashboardLayout>
    );
}

Profile.layout = null;
