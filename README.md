# mpms-api

## O problema.

Ao utilizar os principais sistemas de streaming de música, o usuário está sujeito a algumas limitações
que não gostaria de sofrer.

Abaixo listo as principais, e que me motivaram na criação deste projeto.

- Músicas que estão disponíveis em uma plataforma, mas não está em outra
- Upload de músicas para a plataforma pode ser um problema ou limitação
- Por conta do motivo acima, pode-se haver o problema na criação de playlists incompletas
- Não há opção de alterar informação ou adicionar observações ou marcações com total liberdade
- A plataforma pode a qualquer momento alterar sua utilização, layout e features
- Pode-se haver a remoção de músicas e features
- Nenhum controle e acesso aos dados estatísticos do usuário e algoritmo que realiza as IA (sugestão de músicas, por ex)
- Não poder limpar os dados de buscas com precisão e nem deixar opções padrões
  
## O projeto.

Consiste em implementar uma solução online de streaming de música, de forma privada. Ou seja, não será uma rede social.

O projeto básico consiste no seguinte modelo:

- Banco de dados (arquivos contendo dados da playlist, faixas e músicas)
- Storage (local que armazenará os arquivos originais)
- API (sistema que se comunicará com o banco de dados e o storage)
- Player (sistema web que reproduzirá informações do banco de dados e tocará o arquivo de áudio)

## Expansão do projeto.

Inicialmente irei utilizar as primeiras soluções práticas e que estão um pouco limitadas ao meu conhecimento atual.
Porém, o objetivo é que o projeto se desenvolva e receba mais microservices. 
Também é prevista a integração deste projeto aos serviços da AWS

## Instalação

### GIT

- Instale o GIT, caso não tenha instalado.
- Baixe o Projeto em sua máquina `git clone...`

### DOCKER

- Instale o Docker, caso não tenha instalado.
- Entre no diretório /docker
- Crie uma cópia do arquivo `docker-compose.yml.example` e nomeie-o como `docker-compose.yml`
- Rode o comando `docker-compose up`

### LARAVEL

- Crie uma cópia do arquivo `.env.example` e nomeie-o como `.env`
- Execute a imagem docker rodando o comando `docker exec -it mpms.api /bin/bash`
- Rode o comando `composer install`
- Rode o comando `php artisan migrate`

## Acessos

### Acessar Aplicação

- Pelo terminal: `docker exec -it mpms.api /bin/bash`
- Pelo navegador: `http://localhost:8080`

### Acessar banco de dados (troubleshooting)

- Utilizar dados que está no `docker-compose.yml` em 'environment'
- Confrontar estas informações com o `.env` (parâmetros do DB)
- Se atentar que no `docker-compose.yml.example` a porta utilizada é a 33066

## Endpoints

### CRUD

#### Songs

- List All Songs `GET /api/song`
- Get Song `GET /api/song/{id}`
- Create Song `POST /api/song`
- Update Song `PUT /api/song/{id}`
- Delete Song `DELETE /api/song/{id}`

#### Tracks

- List All Tracks `GET /api/track`
- Get Track `GET /api/track/{id}`
- Create Track `POST /api/track`
- Update Track `PUT /api/track/{id}`
- Delete Track `DELETE /api/track/{id}`

##### Playlists

- List All Playlists `GET /api/playlist`
- Get Playlist `GET /api/playlist/{id}`
- Create Playlist `POST /api/playlist`
- Update Playlist `PUT /api/playlist/{id}`
- Delete Playlist `DELETE /api/playlist/{id}`

### SERVICES

#### Extract

- Extract Data `POST /api/extract`
