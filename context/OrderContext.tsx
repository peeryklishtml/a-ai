import React, { createContext, useContext, useState, ReactNode } from 'react';
import { CartItem, CustomerData, AddressData } from '@/types';

interface OrderContextType {
    cart: CartItem | null;
    setCart: (cart: CartItem | null) => void;
    customer: CustomerData | null;
    setCustomer: (customer: CustomerData) => void;
    address: AddressData | null;
    setAddress: (address: AddressData) => void;
    upsells: { water: boolean; brownie: boolean };
    setUpsells: (upsells: { water: boolean; brownie: boolean }) => void;
    total: number;
    setTotal: (total: number) => void;
}

const OrderContext = createContext<OrderContextType | undefined>(undefined);

export function OrderProvider({ children }: { children: ReactNode }) {
    const [cart, setCart] = useState<CartItem | null>(null);
    const [customer, setCustomer] = useState<CustomerData | null>(null);
    const [address, setAddress] = useState<AddressData | null>(null);
    const [upsells, setUpsells] = useState({ water: false, brownie: false });
    const [total, setTotal] = useState(0);

    return (
        <OrderContext.Provider
            value={{
                cart,
                setCart,
                customer,
                setCustomer,
                address,
                setAddress,
                upsells,
                setUpsells,
                total,
                setTotal,
            }}
        >
            {children}
        </OrderContext.Provider>
    );
}

export function useOrder() {
    const context = useContext(OrderContext);
    if (context === undefined) {
        throw new Error('useOrder must be used within an OrderProvider');
    }
    return context;
}
