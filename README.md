# flute-messenger
Pour des messages qui sonnent bien !

# API Reference
Une version PostMan de la documentation est disponible :
https://documenter.getpostman.com/view/5526749/RWgnWfJF

## Authentification
Afin d'authentifier les requêtes, vous devez utiliser un token d'API valide. Pour obetnir un token, utilisez [l'url get user](#api_get_user)
```javascript
const Header = {
  'X-AUTH-TOKEN': String('<apiToken>')
}
```
## User
### Format
```js
const User = {
  id: Number,
  username: String,
  token: String
}
```
```js
const PublicUser = {
  id: Number,
  username: String
}
```
<h3 id="api_get_user">api/user/{username} [GET]</h3>

Retourne une instance existante de `User` ou en crée une.

**Il est impératif d'utiliser cette URL pour obtenir votre `token`**

### api/user/find/{term} \[GET\]
Soumet une requête de recherche d'un `User` par `username`
#### Response
```js
const response = [PublicUser?, /*...*/]
```

## Conversations
### Format
```js
const Conversation = {
  id: Number,
  libelle: String,
  participants: [User, /*...*/],
  lastMessage: Message,
}
```
### api/conversation \[POST\]
Création d'une nouvelle conversation
#### Request
```js
const newConversation = {
  libelle?: String,
  participants: [Number, /*...*/]  // Au moins 1 User est obligatoire
}
```
#### Response
Une instance de `Conversation`

### api/user/conversations \[GET\]
Récupère la liste des conversations de `User`
#### Response
```js
const response = [Conversation?, /*...*/]
```

### api/conversation/{idConversation} \[GET\]
Récupère les 10 derniers messages d'une conversation
#### Response
```js
const response = [Message?, /*...*/]
```

### api/conversation/{idConversation}/before/{idMessage} \[GET\]
Récupère jusqu'à 10 messages d'une conversation, antérieur au `Message` désigné par `idMessage`
#### Response
```js
const response = [Message?, /*...*/]
```

## Message
### Format
```js
const Message = {
  id: Number,
  texte: String,
  dateEtHeure: String   // DateTime format YYYY-MM-DD HH:mm:ss
}
```
### api/conversation/{idConversation}/message \[POST\]
Transmet un nouveau message dans une conversation
#### Request
```js
const newMessage = {
  texte: String
}
```
#### Response
Une instance de `Message`
