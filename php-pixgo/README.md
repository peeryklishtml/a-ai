# Integração PixGo API - Versão PHP

Este diretório contém os arquivos PHP para integração com a API PixGo.

## ⚠️ IMPORTANTE: Renomear Arquivos

Os arquivos estão salvos como `.txt` para evitar conflito com o `.gitignore`. 

**Para usar, renomeie:**
- `create-payment.txt` → `create-payment.php`
- `check-status.txt` → `check-status.php`

## 📁 Arquivos

### create-payment.php
Endpoint para criar pagamentos Pix via PixGo API.

**Método:** POST  
**URL:** `/php-pixgo/create-payment.php`

**Parâmetros POST:**
```php
$_POST['total_amount']      // float - Valor total (mínimo R$ 10,00)
$_POST['customer_name']     // string - Nome do cliente
$_POST['customer_email']    // string - Email do cliente
$_POST['customer_cpf']      // string - CPF do cliente
$_POST['customer_phone']    // string - Telefone do cliente
$_POST['customer_address']  // string - Endereço completo
$_POST['product_name']      // string - Nome do produto
$_POST['external_id']       // string - ID externo (opcional)
```

**Resposta de Sucesso (201):**
```json
{
  "success": true,
  "qr_code": "00020126580014BR.GOV.BCB.PIX...",
  "qr_image_url": "https://pixgo.org/qr/dep_xxxxx.png",
  "payment_id": "dep_1234567890abcdef",
  "external_id": "order_123",
  "amount": 25.50,
  "status": "pending",
  "expires_at": "2025-01-15T12:20:00",
  "created_at": "2025-01-15T12:00:00"
}
```

**Resposta de Erro (400/500):**
```json
{
  "success": false,
  "error": "LIMIT_EXCEEDED",
  "message": "Valor excede seu limite atual de R$ 300,00",
  "current_limit": 300.00,
  "amount_requested": 500.00
}
```

### check-status.php
Endpoint para verificar o status de um pagamento.

**Método:** GET  
**URL:** `/php-pixgo/check-status.php?payment_id=dep_xxxxx`

**Parâmetros GET:**
```
payment_id - ID do pagamento retornado na criação
```

**Resposta de Sucesso (200):**
```json
{
  "success": true,
  "payment_id": "dep_1234567890abcdef",
  "external_id": "order_123",
  "amount": 25.50,
  "status": "completed",
  "customer_name": "João Silva",
  "customer_cpf": "12345678901",
  "customer_phone": "(11) 99999-9999",
  "created_at": "2025-01-15 12:00:00",
  "updated_at": "2025-01-15 12:15:30"
}
```

**Status Possíveis:**
- `pending` - Aguardando pagamento
- `completed` - Pagamento confirmado
- `expired` - Pagamento expirado (20 minutos)
- `cancelled` - Pagamento cancelado

## 🔑 Configuração da API Key

**IMPORTANTE:** Substitua a API Key placeholder nos arquivos PHP:

```php
define('PIXGO_API_KEY', 'pk_SUA_CHAVE_REAL_AQUI');
```

### Como obter sua API Key:

1. Acesse [pixgo.org](https://pixgo.org)
2. Crie sua conta
3. Valide suas informações da carteira Liquid
4. Vá em "Checkouts"
5. Gere sua chave API de produção

## 📊 Limites da API PixGo

- **Valor mínimo por QR Code:** R$ 10,00
- **Valor máximo por QR Code:** R$ 3.000,00
- **Limite diário por CPF/CNPJ:** R$ 6.000,00
- **Expiração do QR Code:** 20 minutos
- **Rate limit (status check):** 1.000 requisições/24h
- **Sistema de limites progressivos:** 7 níveis baseados em histórico

## 🔄 Exemplo de Uso Completo

### 1. Criar Pagamento (JavaScript)

```javascript
const formData = new FormData();
formData.append('total_amount', '25.50');
formData.append('customer_name', 'João Silva');
formData.append('customer_email', 'joao@exemplo.com');
formData.append('customer_cpf', '12345678901');
formData.append('customer_phone', '(11) 99999-9999');
formData.append('customer_address', 'Rua das Flores, 123, Centro, São Paulo, SP, 01234-567');
formData.append('product_name', 'Açaí 500ml');
formData.append('external_id', 'acai_' + Date.now());

fetch('/php-pixgo/create-payment.php', {
    method: 'POST',
    body: formData
})
.then(res => res.json())
.then(data => {
    if (data.success) {
        console.log('QR Code:', data.qr_code);
        console.log('Payment ID:', data.payment_id);
        // Exibir QR Code para o cliente
    } else {
        console.error('Erro:', data.message);
    }
});
```

### 2. Verificar Status (JavaScript)

```javascript
function checkPaymentStatus(paymentId) {
    fetch(`/php-pixgo/check-status.php?payment_id=${paymentId}`)
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            console.log('Status:', data.status);
            if (data.status === 'completed') {
                // Pagamento confirmado!
                alert('Pagamento confirmado!');
            }
        }
    });
}

// Verificar a cada 5 segundos
const paymentId = 'dep_1234567890abcdef';
const interval = setInterval(() => {
    checkPaymentStatus(paymentId);
}, 5000);

// Parar após 20 minutos (expiração)
setTimeout(() => clearInterval(interval), 1200000);
```

## 🔔 Webhooks (Opcional)

Para receber notificações automáticas, adicione `webhook_url` ao criar o pagamento:

```php
$paymentData = [
    'amount' => 25.50,
    'description' => 'Produto XYZ',
    'webhook_url' => 'https://seusite.com/webhook/pixgo.php'
];
```

**Eventos disponíveis:**
- `payment.completed` - Pagamento confirmado
- `payment.expired` - Pagamento expirado
- `payment.refunded` - Pagamento reembolsado

## 📝 Notas Importantes

1. **Sem ambiente de testes:** PixGo não possui ambiente sandbox. Todas as chaves são de produção.

2. **Validação de CPF:** A API remove automaticamente caracteres não numéricos do CPF.

3. **Timeout:** Requests têm timeout de 30s (criação) e 10s (status).

4. **HTTPS Recomendado:** Use HTTPS em produção para segurança.

5. **Error Handling:** Sempre trate erros de conexão e respostas da API.

## 🆘 Suporte

- **Documentação oficial:** [pixgo.org/docs](https://pixgo.org)
- **Suporte:** Disponível via email ou grupo Telegram (acesse pelo dashboard)
- **Rate Limit:** Se precisar aumentar o limite de 1.000 req/24h, contate o suporte

## 🔐 Segurança

- ✅ Nunca exponha sua API Key no frontend
- ✅ Use HTTPS em produção
- ✅ Valide todos os dados antes de enviar
- ✅ Implemente rate limiting no seu servidor
- ✅ Registre todas as transações em logs
