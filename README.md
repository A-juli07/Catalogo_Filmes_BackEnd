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

Edite o .env com suas configurações.


### 3. Suba a aplicação 
```bash
docker-compose up -d
```

### 4. Migração do Banco
```bash
docker exec -it catalog_films php artisan migrate
```
Confirme todas as alternativas.

## Rotas da API

**Autenticação**
- POST /api/register - Registrar usuário
```json
{
  "name": "Nome",
  "email": "email@exemplo.com",
  "password": "senha123"
}
```
- POST /api/login - Login
```json
{
  "email": "email@exemplo.com",
  "password": "senha123"
}
```

### As seguintes rotas precisam de autenticação, ao realizar o login, copiar o token e enserir como Authentication Bearer.

**Favoritos**
- GET /api/favorites - Lista filmes favoritos ( Aceita filtro por gênero )

- POST /api/favorites - Adiciona um filme aos favoritos
```json
{
  "movie_id": 900667
}
```
- DELETE /api/favorites/{movie_id} - Remover um filme dos favoritos

**Favoritos**
- GET /api/movies/search - Busca Filme por nome
- GET /api/movies/{id} - Busca detalhes de um filme

## Documentação da API (Swagger)


A documentação da API está disponível via Swagger:
### Para as rotas com autenticação e realizar o login copiar o token e enserir em Authorize Bearer
🔗 Acesse: http://localhost:8090/api/documentation

