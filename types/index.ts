export interface Product {
    id: string;
    name: string;
    price: number;
    oldPrice?: number;
    image: string;
    description: string;
}

export interface CustomizationOption {
    id: string;
    name: string;
    price: number;
    category: 'base' | 'fruits' | 'free_toppings' | 'premium' | 'caldas';
}

export interface CartItem {
    product: Product;
    customizations: {
        base?: CustomizationOption;
        fruits: CustomizationOption[];
        freeToppings: CustomizationOption[];
        premium: CustomizationOption[];
        caldas: CustomizationOption[];
    };
    total: number;
}

export interface CustomerData {
    name: string;
    email: string;
    phone: string;
    cpf: string;
}

export interface AddressData {
    cep: string;
    street: string;
    number: string;
    complement?: string;
    neighborhood: string;
    city: string;
    uf: string;
}

export interface OrderData {
    customer: CustomerData;
    address: AddressData;
    cart: CartItem;
    upsells: {
        water: boolean;
        brownie: boolean;
    };
    total: number;
}

export interface PaymentResponse {
    qrCodeText: string;
    transactionId: string;
    amount: number;
}
