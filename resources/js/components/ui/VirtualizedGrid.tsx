import React, { useMemo, useState, useCallback } from 'react';
import { motion, AnimatePresence } from 'framer-motion';

interface VirtualizedGridProps<T> {
    items: T[];
    renderItem: (item: T, index: number) => React.ReactNode;
    itemsPerPage?: number;
    gridCols?: string;
    searchTerm?: string;
    selectedCategory?: number | null;
    filterFunction?: (item: T, searchTerm: string, category: number | null) => boolean;
    className?: string;
}

export function VirtualizedGrid<T extends { id: number; nombre: string; categoria_id?: number }>({
    items,
    renderItem,
    itemsPerPage = 12,
    gridCols = "grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4",
    searchTerm = '',
    selectedCategory = null,
    filterFunction,
    className = ''
}: VirtualizedGridProps<T>) {
    const [currentPage, setCurrentPage] = useState(1);

    // Filtrar items
    const filteredItems = useMemo(() => {
        return items.filter(item => {
            if (filterFunction) {
                return filterFunction(item, searchTerm, selectedCategory);
            }
            
            const matchesSearch = item.nombre.toLowerCase().includes(searchTerm.toLowerCase());
            const matchesCategory = !selectedCategory || item.categoria_id === selectedCategory;
            return matchesSearch && matchesCategory;
        });
    }, [items, searchTerm, selectedCategory, filterFunction]);

    // Calcular paginación
    const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const currentItems = filteredItems.slice(startIndex, endIndex);

    // Reset página cuando cambian los filtros
    React.useEffect(() => {
        setCurrentPage(1);
    }, [searchTerm, selectedCategory, filteredItems.length]);

    const goToPage = useCallback((page: number) => {
        setCurrentPage(Math.max(1, Math.min(page, totalPages)));
    }, [totalPages]);

    const PaginationButton = ({ page, active, disabled, children, onClick }: {
        page?: number;
        active?: boolean;
        disabled?: boolean;
        children: React.ReactNode;
        onClick: () => void;
    }) => (
        <button
            onClick={onClick}
            disabled={disabled}
            className={`px-3 py-2 text-sm rounded-lg transition-colors ${
                active
                    ? 'bg-red-500 text-white'
                    : disabled
                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                    : 'bg-white text-gray-700 hover:bg-gray-50 border'
            }`}
        >
            {children}
        </button>
    );

    return (
        <div className={className}>
            {/* Items Grid */}
            <motion.div 
                className={`grid ${gridCols} gap-4 mb-6`}
                layout
            >
                <AnimatePresence mode="wait">
                    {currentItems.map((item, index) => (
                        <motion.div
                            key={`${item.id}-${currentPage}`}
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -20 }}
                            transition={{ delay: index * 0.05 }}
                            layout
                        >
                            {renderItem(item, startIndex + index)}
                        </motion.div>
                    ))}
                </AnimatePresence>
            </motion.div>

            {/* Información de resultados */}
            <div className="flex items-center justify-between mb-4">
                <div className="text-sm text-gray-500">
                    Mostrando {startIndex + 1} a {Math.min(endIndex, filteredItems.length)} de {filteredItems.length} productos
                </div>
                
                {filteredItems.length === 0 && (
                    <div className="text-sm text-gray-500">
                        No se encontraron productos
                    </div>
                )}
            </div>

            {/* Paginación */}
            {totalPages > 1 && (
                <div className="flex items-center justify-center gap-2">
                    <PaginationButton
                        disabled={currentPage === 1}
                        onClick={() => goToPage(currentPage - 1)}
                    >
                        ← Anterior
                    </PaginationButton>

                    {/* Números de página */}
                    {Array.from({ length: Math.min(5, totalPages) }, (_, i) => {
                        const page = Math.max(1, Math.min(totalPages - 4, currentPage - 2)) + i;
                        if (page > totalPages) return null;
                        
                        return (
                            <PaginationButton
                                key={page}
                                page={page}
                                active={currentPage === page}
                                onClick={() => goToPage(page)}
                            >
                                {page}
                            </PaginationButton>
                        );
                    })}

                    {totalPages > 5 && currentPage < totalPages - 2 && (
                        <>
                            <span className="px-2 text-gray-400">...</span>
                            <PaginationButton onClick={() => goToPage(totalPages)}>
                                {totalPages}
                            </PaginationButton>
                        </>
                    )}

                    <PaginationButton
                        disabled={currentPage === totalPages}
                        onClick={() => goToPage(currentPage + 1)}
                    >
                        Siguiente →
                    </PaginationButton>
                </div>
            )}

            {/* Items por página */}
            <div className="flex items-center justify-center mt-4">
                <select
                    value={itemsPerPage}
                    onChange={(e) => {
                        const newItemsPerPage = parseInt(e.target.value);
                        setCurrentPage(1);
                    }}
                    className="text-sm border rounded-lg px-3 py-1 bg-white"
                >
                    <option value={12}>12 por página</option>
                    <option value={24}>24 por página</option>
                    <option value={48}>48 por página</option>
                    <option value={96}>96 por página</option>
                </select>
            </div>
        </div>
    );
}