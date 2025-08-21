# Integração com FakeStore API

Este projeto implementa uma integração com a [FakeStore API](https://fakestoreapi.com), permitindo sincronizar produtos e categorias para um banco de dados local em Laravel, além de disponibilizar endpoints para consultas com filtros.

---

## 🚀 Funcionalidades

-   Sincronização de produtos e categorias da FakeStore API
-   Armazenamento no banco de dados local
-   Endpoints para consulta de produtos e categorias
-   Filtros avançados para busca de produtos
-   Estrutura preparada para expansão

---

## 🛠️ Tecnologias

-   PHP (Laravel)
-   MySQL
-   Composer

---

## 📂 Estrutura do Projeto

-   `app/Models/Product.php` → Model de produtos
-   `app/Models/Category.php` → Model de categorias
-   `app/Services/FakeStoreClient.php` → Cliente HTTP para integração
-   `routes/api.php` → Definição das rotas da API

---

## ⚙️ Como rodar o projeto

### 1. Clonar o repositório

```bash
git clone https://github.com/seu-usuario/seu-repositorio.git
cd seu-repositorio
```

### 2. Instalar dependências

```bash
composer install
```

### 3. Configurar variáveis de ambiente

Copie o arquivo de exemplo e configure o `.env`:

```bash
cp .env.example .env
```

Em seguida, gere a chave de aplicação do Laravel com o comando:

```bash
php artisan key:generate
```

### 4. Rodar migrações

```bash
php artisan migrate
```

### 5. Iniciar o servidor

```bash
php artisan serve
```

A API estará disponível em `http://127.0.0.1:8000`

---

## 🔄 Sincronização com a FakeStore

Para importar produtos e categorias:

```http
POST /api/integrations/fakestore/sync
```

**Exemplo de resposta:**

```json
{
    "message": "Synchronization completed",
    "imported_products": 20,
    "imported_categories": 4
}
```

---

## 📡 Endpoints Disponíveis

### Listar todos os produtos

```http
GET /api/products
```

### Buscar produtos com filtros

```http
GET /api/products?category=electronics&min_price=50&max_price=200&q=phone
```

### Listar todas as categorias

```http
GET /api/categories
```

### Buscar produtos por categoria

```http
GET /api/categories/{id}/products
```

---

## 🗄️ Decisão de Modelagem

### Tabelas

-   **products**

    -   `id` (PK)
    -   `category_id` (FK para categories)
    -   `external_id` (referência da FakeStore API)
    -   `title`, `description`, `price`, `image_url`
    -   `raw` (JSON com dados originais da API)

-   **categories**
    -   `id` (PK)
    -   `name`

### Índices criados

-   Índice em `products.category_id` (para buscas por categoria)
-   Índice em `products.external_id` (para evitar duplicação na sincronização)
-   Índice em `categories.name` (para consultas rápidas por nome)

---

## 🧪 Testando no Postman

### Exemplo de filtros

```http
GET /api/products?category=men's clothing&min_price=20&max_price=100&q=shirt
```

Resposta esperada: lista de produtos da categoria "men's clothing", com preço entre 20 e 100, cujo título ou descrição contenha "shirt".

---

## 👨‍💻 Para desenvolvedores que baixarem do GitHub

Se você baixou o projeto diretamente do GitHub:

1. Execute `composer install`
2. Configure seu `.env`
3. Rode `php artisan migrate`
4. Inicie o servidor com `php artisan serve`
5. Faça a sincronização com `POST /api/integrations/fakestore/sync` antes de listar produtos

---

## 📌 Observações

-   O projeto **não precisa de CRUD manual** para produtos e categorias. Eles são sincronizados da FakeStore.
-   Apenas **endpoints de consulta e sincronização** foram implementados, como solicitado.

---

## 📖 Licença

Este projeto é de uso livre para estudos e práticas de integração com APIs externas.
