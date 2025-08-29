import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';

interface HybridPageProps {
    title: string;
    breadcrumbs?: BreadcrumbItem[];
    htmlContent: string;
    scripts?: string;
}

export default function HybridPage({ title, breadcrumbs = [], htmlContent, scripts }: HybridPageProps) {
    const contentRef = useRef<HTMLDivElement>(null);
    const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        if (!contentRef.current) return;

        // Función para manejar clics en enlaces internos
        const handleLinkClick = (e: MouseEvent) => {
            const target = e.target as HTMLElement;
            const link = target.closest('a');
            
            if (link && link.href && !link.target && link.href.startsWith(window.location.origin)) {
                e.preventDefault();
                const path = link.href.replace(window.location.origin, '');
                router.visit(path);
            }
        };

        // Función para manejar envío de formularios
        const handleFormSubmit = async (e: Event) => {
            e.preventDefault();
            const form = e.target as HTMLFormElement;
            
            setIsLoading(true);
            
            try {
                const formData = new FormData(form);
                const isMultipart = form.enctype === 'multipart/form-data';
                
                let body: any;
                let headers: any = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                };
                
                if (isMultipart) {
                    body = formData;
                } else {
                    body = JSON.stringify(Object.fromEntries(formData));
                    headers['Content-Type'] = 'application/json';
                }
                
                const response = await fetch(form.action || window.location.href, {
                    method: form.method?.toUpperCase() || 'POST',
                    body: body,
                    headers: headers
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Recargar la página con Inertia para mantener el estado
                    router.reload();
                } else {
                    alert(data.message || 'Error al procesar la solicitud');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            } finally {
                setIsLoading(false);
            }
        };

        // Adjuntar event listeners
        contentRef.current.addEventListener('click', handleLinkClick);
        
        const forms = contentRef.current.querySelectorAll('form');
        forms.forEach(form => {
            // Solo manejar formularios que no tienen action externa
            if (!form.action || form.action.startsWith(window.location.origin)) {
                form.addEventListener('submit', handleFormSubmit);
            }
        });

        // Ejecutar scripts embebidos
        const scriptElements: HTMLScriptElement[] = [];
        const scripts = contentRef.current.querySelectorAll('script');
        scripts.forEach(originalScript => {
            const newScript = document.createElement('script');
            
            if (originalScript.src) {
                newScript.src = originalScript.src;
            } else {
                newScript.innerHTML = originalScript.innerHTML;
            }
            
            // Copiar atributos
            Array.from(originalScript.attributes).forEach(attr => {
                newScript.setAttribute(attr.name, attr.value);
            });
            
            document.body.appendChild(newScript);
            scriptElements.push(newScript);
            
            // Remover el script original del contenido
            originalScript.remove();
        });

        // Limpiar al desmontar
        return () => {
            if (contentRef.current) {
                contentRef.current.removeEventListener('click', handleLinkClick);
                forms.forEach(form => {
                    form.removeEventListener('submit', handleFormSubmit);
                });
            }
            
            // Remover scripts agregados
            scriptElements.forEach(script => {
                if (script.parentNode) {
                    script.parentNode.removeChild(script);
                }
            });
        };
    }, [htmlContent]);

    // Recargar cuando cambie el contenido
    useEffect(() => {
        if (contentRef.current && htmlContent) {
            // Disparar evento personalizado para notificar que el contenido cambió
            const event = new CustomEvent('hybrid-content-loaded');
            window.dispatchEvent(event);
        }
    }, [htmlContent]);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={title} />
            
            {isLoading && (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div className="bg-white p-4 rounded-lg">
                        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    </div>
                </div>
            )}
            
            {/* Contenedor para el HTML de Laravel/Blade */}
            <div 
                ref={contentRef}
                className="hybrid-content"
                dangerouslySetInnerHTML={{ __html: htmlContent }}
            />
            
            <style>{`
                .hybrid-content {
                    animation: fadeIn 0.3s ease-in;
                }
                
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                
                .hybrid-content form[data-loading="true"] {
                    opacity: 0.6;
                    pointer-events: none;
                }
            `}</style>
        </AppLayout>
    );
}