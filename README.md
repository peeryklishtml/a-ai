# Zé do Açaí - E-commerce

Sistema completo de e-commerce para venda de açaí com customização de produtos, checkout e pagamento via Pix.

## 🚀 Tecnologias

- **Next.js 14** - Framework React
- **TypeScript** - Tipagem estática
- **CSS Modules** - Estilização modular
- **PixGo API** - Integração de pagamento Pix
- **QRCode.react** - Geração de QR codes

## 📦 Instalação

```bash
# Instalar dependências
npm install

# Rodar em desenvolvimento
npm run dev

# Build para produção
npm run build

# Iniciar servidor de produção
npm start
```

## 🌐 Deploy no Netlify

1. Faça push do código para um repositório Git (GitHub, GitLab, etc.)
2. Conecte seu repositório no Netlify
3. Configure as variáveis de ambiente:
   - `PIXGO_API_KEY`: Sua chave API do PixGo
4. O Netlify detectará automaticamente o Next.js e fará o deploy

### Variáveis de Ambiente

Crie um arquivo `.env.local` na raiz do projeto:

```env
PIXGO_API_KEY=pk_sua_chave_api_aqui
```

**Como obter sua API Key:**
1. Acesse [pixgo.org](https://pixgo.org) e crie sua conta
2. Valide suas informações da carteira Liquid
3. Navegue até a seção "Checkouts"
4. Gere sua chave API de produção

Veja mais detalhes em [`PIXGO_SETUP.md`](PIXGO_SETUP.md)

## 📁 Estrutura do Projeto

```
├── pages/
│   ├── _app.tsx           # App wrapper com Context
│   ├── index.tsx          # Página inicial (produtos)
│   ├── customize.tsx      # Customização do açaí
│   ├── checkout.tsx       # Checkout multi-step
│   ├── payment.tsx        # Página de pagamento Pix
│   └── api/
│       └── create-payment.ts  # API route para criar pagamento
├── styles/
│   ├── globals.css        # Estilos globais
│   ├── Home.module.css
│   ├── Customize.module.css
│   ├── Checkout.module.css
│   └── Payment.module.css
├── context/
│   └── OrderContext.tsx   # Context para gerenciar pedido
├── data/
│   └── products.ts        # Dados de produtos e opções
├── types/
│   └── index.ts           # Definições TypeScript
├── package.json
├── tsconfig.json
├── next.config.js
└── netlify.toml
```

## 🎨 Funcionalidades

- ✅ Listagem de produtos com promoções
- ✅ Customização completa do açaí (20+ opções)
- ✅ Checkout multi-step (Dados Pessoais → Endereço → Upsells)
- ✅ Integração com ViaCEP para busca de endereço
- ✅ Pagamento via Pix com QR Code
- ✅ Timer de expiração do pagamento
- ✅ Design responsivo e moderno

## 🔧 Customização

### Adicionar Novos Produtos

Edite o arquivo `data/products.ts`:

```typescript
export const products: Product[] = [
  {
    id: 'novo-produto',
    name: 'Novo Produto',
    price: 30.00,
    oldPrice: 50.00,
    image: 'url-da-imagem',
    description: 'Descrição do produto'
  },
  // ...
];
```

### Adicionar Novas Opções de Customização

Edite o arquivo `data/products.ts`:

```typescript
export const customizationOptions: CustomizationOption[] = [
  {
    id: 'nova-opcao',
    name: 'Nova Opção',
    price: 2.00,
    category: 'premium' // base | fruits | free_toppings | premium | caldas
  },
  // ...
];
```

## 📝 Notas Importantes

- As credenciais da API Dice estão hardcoded em `pages/api/create-payment.ts`
- Para produção, mova as credenciais para variáveis de ambiente
- O projeto está configurado para deploy automático no Netlify
- Todos os arquivos PHP originais foram convertidos para TSX

## 🆘 Suporte

Para problemas ou dúvidas, consulte a documentação do Next.js: https://nextjs.org/docs
