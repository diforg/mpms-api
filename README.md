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
  
