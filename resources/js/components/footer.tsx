import { Link } from '@inertiajs/react';

export function Footer() {
    return (
        <footer className="border-t border-border/40 bg-background">
            <div className="container mx-auto px-4 py-12">
                <div className="grid gap-8 md:grid-cols-4">
                    <div className="space-y-4">
                        <Link href="/" className="flex items-center">
                            <img src="/images/logo-black.webp" alt="3AG" className="h-8 w-auto dark:hidden" />
                            <img src="/images/logo-white.webp" alt="3AG" className="hidden h-8 w-auto dark:block" />
                        </Link>
                        <p className="text-sm text-muted-foreground">Premium WordPress plugins and themes to power your website.</p>
                    </div>

                    <div className="space-y-4">
                        <h4 className="text-sm font-semibold">Products</h4>
                        <ul className="space-y-2">
                            <li>
                                <Link href="/products" className="text-sm text-muted-foreground transition-colors hover:text-foreground">
                                    All Products
                                </Link>
                            </li>
                            <li>
                                <Link href="/#features" className="text-sm text-muted-foreground transition-colors hover:text-foreground">
                                    Features
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div className="space-y-4">
                        <h4 className="text-sm font-semibold">Account</h4>
                        <ul className="space-y-2">
                            <li>
                                <Link href="/login" className="text-sm text-muted-foreground transition-colors hover:text-foreground">
                                    Login
                                </Link>
                            </li>
                            <li>
                                <Link href="/register" className="text-sm text-muted-foreground transition-colors hover:text-foreground">
                                    Register
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div className="space-y-4">
                        <h4 className="text-sm font-semibold">Legal</h4>
                        <ul className="space-y-2">
                            <li>
                                <Link href="/privacy" className="text-sm text-muted-foreground transition-colors hover:text-foreground">
                                    Privacy Policy
                                </Link>
                            </li>
                            <li>
                                <Link href="/terms" className="text-sm text-muted-foreground transition-colors hover:text-foreground">
                                    Terms of Service
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>

                <div className="mt-8 border-t border-border/40 pt-8">
                    <p className="text-center text-sm text-muted-foreground">© {new Date().getFullYear()} 3AG. All rights reserved.</p>
                </div>
            </div>
        </footer>
    );
}
