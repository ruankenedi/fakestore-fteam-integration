# Integração com FakeStore API

Este projeto implementa uma integração com a [FakeStore API](https://fakestoreapi.com), permitindo sincronizar produtos e categorias para um banco de dados local em Laravel, além de disponibilizar endpoints para consultas com filtros.

---

## 🚀 Funcionalidades

-   Sincronização de produtos e categorias da FakeStore API
-   Armazenamento no banco de dados local
-   Endpoints para consulta de produtos e categorias
-   Filtros avançados para busca de produtos
-   Estrutura preparada para expansão
-   Execução com Docker e sem ele

---

## 🛠️ Tecnologias

-   PHP (Laravel)
-   MySQL
-   Composer
-   Docker

---

## 📂 Estrutura do Projeto

-   `app/Console/Commands/FakeStoreSync.php` → Sincronização com a FakeStore
-   `app/Models/Product.php` → Model de produtos
-   `app/Models/Category.php` → Model de categorias
-   `app/Services/FakeStoreClient.php` → Cliente HTTP para integração
-   `routes/api.php` → Definição das rotas da API

---

## ⚙️ Como rodar o projeto

## ⚙️ Passo a Passo — Sem Docker

### 1. Clone o repositório

```bash
git clone https://github.com/ruankenedi/fakestore-fteam-integration.git
cd fakestore-fteam-integration
```

### 2. Instale dependências

```bash
composer install
```

### 3. Configure o `.env`

Copie o arquivo de exemplo:

```bash
cp .env.example-no-docker .env
```

### 4. Gerar App Key

```bash
php artisan key:generate
```

### 5. Criar o banco e rodar as migrações

Certifique-se de que o MySQL está rodando localmente via XAMPP ou similar dependendo do seu sistema.

```bash
php artisan migrate
```

### 6. Sincronizar produtos e categorias

```bash
php artisan fakestore:sync
```

### 7. Rodar servidor Laravel

```bash
php artisan serve

```

API disponível em: http://127.0.0.1:8000

---

## ⚙️ Passo a Passo — Com Docker

### 1. Clonar o repositório

```bash
git clone https://github.com/ruankenedi/fakestore-fteam-integration.git
cd fakestore-fteam-integration
```

### 2. Criar arquivo `.env`

```bash
cp .env.example .env
```

### 3. Docker Compose

Antes, se certifique que o docker está `instalado` e `rodando` na sua máquina.

Depois de confirmar o requisito acima, segue executando os comandos abaixo na sequência.

```bash
docker compose up -d --build
```

### 4. Acessar o container da aplicação

```bash
docker exec -it fakestore_app bash
```

### 5. Instalar dependências dentro do container

```bash
composer install
```

Em seguida, gere a chave de aplicação do Laravel com o comando para preencher o campo APP_KEY do arquivo `.env`:

```bash
php artisan key:generate
```

### 6. Rodar migrações

```bash
php artisan migrate
```

### 7. Sincronizar FakeStore API

Caso já tenha sincronizado em algum momento antes, pode ser que as categorias venha como 0.

```bash
php artisan fakestore:sync
```

### 8. Iniciar servidor Laravel (dentro do container)

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

---

## 🔄 Sincronização com a FakeStore caso queira fazer a parte

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
GET /api/products?category=electronics&min_price=50&max_price=200&search=Women
```

### Listar todas as categorias

```http
GET /api/categories
```

### Buscar produtos por categoria

```http
GET /api/categories/{id}/
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
GET /api/products?category=men's clothing&min_price=20&max_price=100&search=shirt
```

Resposta esperada: lista de produtos da categoria "men's clothing", com preço entre 20 e 100, cujo título ou descrição contenha "shirt".

---

## 📌 Observações

-   O projeto **não precisa de CRUD manual** para produtos e categorias. Eles são sincronizados da FakeStore.
-   Apenas **endpoints de consulta e sincronização** foram implementados, como solicitado.

---

## 📖 Licença

Este projeto é de uso livre para estudos e práticas de integração com APIs externas.
