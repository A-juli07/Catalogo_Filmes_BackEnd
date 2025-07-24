# Catálogo de Filmes - Backend

## Funcionalidades

- Buscar filmes pelo nome via API do TMDB
- Adicionar filmes aos favoritos (armazenamento local)
- Listar favoritos com filtro por gênero
- Remover filmes da lista de favoritos
- Detalhamento completo de cada filme
- Registro e autenticação de usuários com JWT

## Como rodar o projeto localmente com Docker

### 1. Clone o repositório

```bash
git clone https://github.com/A-juli07/Catalogo_Filmes_BackEnd.git
cd Catalogo_Filmes_BackEnd
```

### 2. Crie o arquivo .env

```bash
cp .env.example .env
```

### 3. Gere a key da aplicação Laravel

```bash
docker-compose run --rm app php artisan key:generate
```

### 4. Suba a aplicação 
```bash
docker-compose up -d
```

## Documentação da API (Swagger)

A documentação interativa da API está disponível via Swagger:

🔗 Acesse: http://localhost:8090/api/documentation

Com ela, você pode:

- Explorar todos os endpoints disponíveis
- Ver exemplos de payloads e respostas
- Testar requisições diretamente pela interface