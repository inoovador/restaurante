import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { 
    BookOpen, 
    Folder, 
    LayoutGrid, 
    Users, 
    UserCheck,
    Grid3X3,
    Package,
    ShoppingCart,
    ShoppingBag,
    DollarSign,
    BarChart3,
    Utensils,
    Coffee,
    ChefHat
} from 'lucide-react';
import AppLogo from './app-logo';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Usuarios',
        href: '/usuarios',
        icon: Users,
    },
    {
        title: 'Roles',
        href: '/roles',
        icon: UserCheck,
    },
    {
        title: 'Mesas',
        href: '/mesas',
        icon: Grid3X3,
    },
    {
        title: 'Clientes',
        href: '/clientes',
        icon: Users,
    },
    {
        title: 'Productos',
        href: '/productos',
        icon: Package,
    },
    {
        title: 'Categorías',
        href: '/categorias',
        icon: Folder,
    },
    {
        title: 'Compras',
        href: '/compras',
        icon: ShoppingCart,
    },
    {
        title: 'Ventas',
        href: '/ventas',
        icon: ShoppingBag,
    },
    {
        title: 'Caja',
        href: '/caja',
        icon: DollarSign,
    },
    {
        title: 'Barra',
        href: '/barra',
        icon: Coffee,
    },
    {
        title: 'Cocina',
        href: '/cocina',
        icon: ChefHat,
    },
    {
        title: 'Inventario',
        href: '/inventario',
        icon: BarChart3,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
