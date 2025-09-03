import { useState, useEffect } from 'react';

export function useLocalStorage<T>(key: string, initialValue: T): [T, (value: T | ((val: T) => T)) => void] {
    // Estado para almacenar nuestro valor
    // Pasar la función de estado inicial a useState para que la función solo se ejecute una vez
    const [storedValue, setStoredValue] = useState<T>(() => {
        if (typeof window === "undefined") {
            return initialValue;
        }
        try {
            // Obtener del localStorage local por clave
            const item = window.localStorage.getItem(key);
            // Analizar el JSON almacenado o si no existe devolver initialValue
            return item ? JSON.parse(item) : initialValue;
        } catch (error) {
            // Si hay error también devolver initialValue
            console.error(`Error reading localStorage key "${key}":`, error);
            return initialValue;
        }
    });

    // Devolver una versión envuelta de la función setter de useState que persiste el nuevo valor en localStorage
    const setValue = (value: T | ((val: T) => T)) => {
        try {
            // Permitir que el valor sea una función para que tengamos la misma API que useState
            const valueToStore = value instanceof Function ? value(storedValue) : value;
            // Guardar el estado
            setStoredValue(valueToStore);
            // Guardar en localStorage
            if (typeof window !== "undefined") {
                window.localStorage.setItem(key, JSON.stringify(valueToStore));
            }
        } catch (error) {
            // Un caso de uso más avanzado sería manejar el error del caso de almacenamiento
            console.error(`Error setting localStorage key "${key}":`, error);
        }
    };

    return [storedValue, setValue];
}

export function usePersistedCart() {
    const [cart, setCart] = useLocalStorage<any[]>('restaurant_cart', []);

    // Limpiar carrito después de cierto tiempo de inactividad
    useEffect(() => {
        const lastActivity = localStorage.getItem('cart_last_activity');
        const now = Date.now();
        const timeout = 30 * 60 * 1000; // 30 minutos

        if (lastActivity && now - parseInt(lastActivity) > timeout) {
            setCart([]);
            localStorage.removeItem('cart_last_activity');
        }
    }, [setCart]);

    const updateCartActivity = () => {
        localStorage.setItem('cart_last_activity', Date.now().toString());
    };

    const addToCart = (product: any) => {
        setCart(currentCart => {
            const existingItem = currentCart.find(item => item.id === product.id);
            updateCartActivity();
            
            if (existingItem) {
                return currentCart.map(item => 
                    item.id === product.id 
                        ? { ...item, quantity: item.quantity + 1 }
                        : item
                );
            } else {
                return [...currentCart, { ...product, quantity: 1 }];
            }
        });
    };

    const updateQuantity = (productId: number, delta: number) => {
        setCart(currentCart => {
            updateCartActivity();
            return currentCart.map(item => {
                if (item.id === productId) {
                    const newQuantity = item.quantity + delta;
                    return newQuantity > 0 ? { ...item, quantity: newQuantity } : null;
                }
                return item;
            }).filter(Boolean) as any[];
        });
    };

    const removeFromCart = (productId: number) => {
        setCart(currentCart => {
            updateCartActivity();
            return currentCart.filter(item => item.id !== productId);
        });
    };

    const clearCart = () => {
        setCart([]);
        localStorage.removeItem('cart_last_activity');
    };

    return {
        cart,
        addToCart,
        updateQuantity,
        removeFromCart,
        clearCart
    };
}