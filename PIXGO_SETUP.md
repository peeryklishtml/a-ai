# PixGo API Integration - Environment Variables

## Setup Instructions

For production deployment, create a `.env.local` file in the root directory:

```env
# PixGo API Configuration
PIXGO_API_KEY=pk_your_actual_api_key_here
```

## Getting Your API Key

1. Access [pixgo.org](https://pixgo.org) and create your account
2. Validate your Liquid wallet information
3. Navigate to the "Checkouts" section
4. Generate your production API Key
5. Copy the key and add it to your `.env.local` file

## Update API Routes

After adding the environment variable, update the following files:

### pages/api/create-payment.ts
```typescript
const PIXGO_API_KEY = process.env.PIXGO_API_KEY || 'pk_1234567890abcdef...';
```

### pages/api/check-payment-status.ts
```typescript
const PIXGO_API_KEY = process.env.PIXGO_API_KEY || 'pk_1234567890abcdef...';
```

## Important Notes

- ⚠️ **Never commit `.env.local` to Git** (already in `.gitignore`)
- ⚠️ The current hardcoded key is a placeholder - replace with your real key
- ⚠️ For Netlify deployment, add the environment variable in the Netlify dashboard

## Netlify Environment Variables

1. Go to your Netlify site dashboard
2. Navigate to **Site settings** → **Environment variables**
3. Add: `PIXGO_API_KEY` = `pk_your_actual_key`
4. Redeploy your site

## API Features

- ✅ Minimum payment: R$ 10.00
- ✅ QR Code expiration: 20 minutes
- ✅ Automatic status updates via webhooks (optional)
- ✅ Progressive limit system (up to R$ 3,000 per QR Code)
- ✅ Daily limit per CPF: R$ 6,000

## Testing

The PixGo API does not have a separate test environment. All API keys are for production use.
