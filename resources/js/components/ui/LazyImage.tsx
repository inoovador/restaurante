import React, { useState, useRef, useEffect } from 'react';
import { motion } from 'framer-motion';

interface LazyImageProps {
    src: string;
    alt: string;
    className?: string;
    fallbackSrc?: string;
    onLoad?: () => void;
    onError?: () => void;
}

export const LazyImage: React.FC<LazyImageProps> = ({
    src,
    alt,
    className = '',
    fallbackSrc = 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=400&h=300&fit=crop',
    onLoad,
    onError
}) => {
    const [imageLoaded, setImageLoaded] = useState(false);
    const [imageError, setImageError] = useState(false);
    const [inView, setInView] = useState(false);
    const imgRef = useRef<HTMLImageElement>(null);
    const observerRef = useRef<IntersectionObserver>();

    useEffect(() => {
        const currentRef = imgRef.current;
        
        if (currentRef) {
            observerRef.current = new IntersectionObserver(
                ([entry]) => {
                    if (entry.isIntersecting) {
                        setInView(true);
                        observerRef.current?.disconnect();
                    }
                },
                {
                    threshold: 0.1,
                    rootMargin: '50px'
                }
            );
            
            observerRef.current.observe(currentRef);
        }

        return () => {
            observerRef.current?.disconnect();
        };
    }, []);

    const handleImageLoad = () => {
        setImageLoaded(true);
        onLoad?.();
    };

    const handleImageError = () => {
        setImageError(true);
        onError?.();
    };

    const imageSrc = imageError ? fallbackSrc : src;

    return (
        <div ref={imgRef} className={`relative overflow-hidden ${className}`}>
            {/* Skeleton loader */}
            {!imageLoaded && (
                <div className="absolute inset-0 bg-gray-200 animate-pulse">
                    <div className="w-full h-full bg-gradient-to-r from-gray-200 via-gray-100 to-gray-200 animate-pulse" />
                </div>
            )}

            {/* Image */}
            {inView && (
                <motion.img
                    src={imageSrc}
                    alt={alt}
                    className={`w-full h-full object-cover transition-opacity duration-300 ${
                        imageLoaded ? 'opacity-100' : 'opacity-0'
                    }`}
                    onLoad={handleImageLoad}
                    onError={handleImageError}
                    initial={{ opacity: 0 }}
                    animate={{ opacity: imageLoaded ? 1 : 0 }}
                    transition={{ duration: 0.3 }}
                />
            )}

            {/* Loading indicator */}
            {inView && !imageLoaded && !imageError && (
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="w-8 h-8 border-2 border-gray-300 border-t-blue-500 rounded-full animate-spin" />
                </div>
            )}
        </div>
    );
};