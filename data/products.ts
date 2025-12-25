import { Product, CustomizationOption } from '@/types';

export const products: Product[] = [
    {
        id: 'acai-300ml',
        name: 'Açaí 300ml',
        price: 15.00,
        oldPrice: 22.90,
        image: 'https://media.istockphoto.com/id/1155239999/pt/foto/acai-cup-brazilian-famous-fruit.jpg?s=612x612&w=0&k=20&c=iCj7L9-Iq59VAMfRph2O4u4T_j2O_K5L44T2i-9LgKg=',
        description: 'Monte do seu jeito! Escolha frutas e complementos.'
    },
    {
        id: 'acai-500ml',
        name: 'Açaí 500ml',
        price: 20.00,
        oldPrice: 35.00,
        image: 'https://media.istockphoto.com/id/1155239999/pt/foto/acai-cup-brazilian-famous-fruit.jpg?s=612x612&w=0&k=20&c=iCj7L9-Iq59VAMfRph2O4u4T_j2O_K5L44T2i-9LgKg=',
        description: 'O tamanho ideal para sua fome! Completo e delicioso.'
    },
    {
        id: 'acai-700ml',
        name: 'Açaí 700ml',
        price: 25.00,
        oldPrice: 45.90,
        image: 'https://media.istockphoto.com/id/1155239999/pt/foto/acai-cup-brazilian-famous-fruit.jpg?s=612x612&w=0&k=20&c=iCj7L9-Iq59VAMfRph2O4u4T_j2O_K5L44T2i-9LgKg=',
        description: 'Para quem ama Açaí! Muito mais sabor.'
    },
    {
        id: 'barca-1l',
        name: 'Barca 1 Litro',
        price: 45.00,
        oldPrice: 89.90,
        image: 'https://p2.trrsf.com/image/fget/cf/940/0/images.terra.com/2022/12/08/426002167-shutterstock1597876930.jpg',
        description: 'Ideal para dividir! Açaí puro e muitos acompanhamentos.'
    }
];

export const customizationOptions: CustomizationOption[] = [
    // Base Flavors
    { id: 'tradicional', name: 'Açaí Tradicional', price: 0, category: 'base' },
    { id: 'trufado', name: 'Açaí Trufado', price: 2.00, category: 'base' },
    { id: 'morango', name: 'Creme de Morango', price: 0, category: 'base' },
    { id: 'cupuacu', name: 'Creme de Cupuaçu', price: 0, category: 'base' },
    { id: 'misto', name: 'Misto (Açaí + Cupuaçu)', price: 0, category: 'base' },
    { id: 'zero', name: 'Açaí Zero Açúcar', price: 3.00, category: 'base' },

    // Fruits
    { id: 'morango-fruit', name: 'Morango', price: 0, category: 'fruits' },
    { id: 'banana', name: 'Banana', price: 0, category: 'fruits' },
    { id: 'kiwi', name: 'Kiwi', price: 0, category: 'fruits' },
    { id: 'manga', name: 'Manga', price: 0, category: 'fruits' },
    { id: 'abacaxi', name: 'Abacaxi', price: 0, category: 'fruits' },
    { id: 'uva', name: 'Uva Verde', price: 0, category: 'fruits' },

    // Free Toppings
    { id: 'leite-ninho', name: 'Leite Ninho', price: 0, category: 'free_toppings' },
    { id: 'pacoca', name: 'Paçoca', price: 0, category: 'free_toppings' },
    { id: 'granola', name: 'Granola', price: 0, category: 'free_toppings' },
    { id: 'leite-condensado', name: 'Leite Condensado', price: 0, category: 'free_toppings' },
    { id: 'mel', name: 'Mel', price: 0, category: 'free_toppings' },
    { id: 'sucrilhos', name: 'Sucrilhos', price: 0, category: 'free_toppings' },
    { id: 'coco', name: 'Coco Ralado', price: 0, category: 'free_toppings' },
    { id: 'ovomaltine', name: 'Ovomaltine', price: 0, category: 'free_toppings' },
    { id: 'chocoball', name: 'Chocoball', price: 0, category: 'free_toppings' },
    { id: 'confete', name: 'Confete', price: 0, category: 'free_toppings' },
    { id: 'jujuba', name: 'Jujuba', price: 0, category: 'free_toppings' },
    { id: 'amendoim', name: 'Amendoim', price: 0, category: 'free_toppings' },

    // Premium
    { id: 'nutella', name: 'Nutella Original', price: 5.00, category: 'premium' },
    { id: 'kinder', name: 'Kinder Bueno (Pedaços)', price: 6.00, category: 'premium' },
    { id: 'kitkat', name: 'KitKat', price: 4.00, category: 'premium' },
    { id: 'bis', name: 'Bis (Lacta)', price: 3.00, category: 'premium' },
    { id: 'gotas', name: 'Gotas de Chocolate', price: 3.00, category: 'premium' },
    { id: 'chantilly', name: 'Chantilly', price: 4.00, category: 'premium' },
    { id: 'marshmallow', name: 'Marshmallow', price: 3.00, category: 'premium' },
    { id: 'mouse', name: 'Mouse de Maracujá', price: 4.50, category: 'premium' },
    { id: 'ferrero', name: 'Ferrero Rocher (Unidade)', price: 7.00, category: 'premium' },

    // Caldas
    { id: 'calda-morango', name: 'Calda de Morango', price: 0, category: 'caldas' },
    { id: 'calda-chocolate', name: 'Calda de Chocolate', price: 0, category: 'caldas' },
    { id: 'calda-caramelo', name: 'Calda de Caramelo', price: 0, category: 'caldas' },
    { id: 'calda-menta', name: 'Calda de Menta', price: 0, category: 'caldas' },
];
