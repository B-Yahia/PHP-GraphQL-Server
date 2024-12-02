# PHP GraphQL Server

This project is a GraphQL server implementation in PHP, it's an introduction to basic integration of GraphQL into PHP applications. It serves as an example for setting up a GraphQL API with structured queries, mutations.

## Features

  - **GraphQL Queries**: Retrieve data such as single post and list of posts.
  - **GraphQL Mutations**: Modify data like creating, updating, and deleting posts.
  - **Structured Backend**: A clean and modular design.

## Installation

1. Clone the repository and install dependencies:
```
git clone https://github.com/B-Yahia/PHP-GraphQL-Server.git  
cd PHP-GraphQL-Server   
composer install  
```
2. Create a MySQL database and run the SQL script in the file `src/Util/Database.sql` to create the required tables.
3. Set up the `.env` file using `.env.example` as a template.

## GraphQL API Overview

### Queries
  1. `posts`: Fetches all posts.
```
query GetPosts {
    posts {
        id
        title
        content
        author
    }
}

```
  2. `post`: Fetches a single post by ID.
```
query GetPost($id: Int!) {
    post(id: $id) {
        id
        title
        content
        author
    }
}

```
#### Mutations 
  1. `addPost`: Creates a new post.
```
mutation AddPost($title: String!, $content: String!, $author: String!) {
    addPost(title: $title, content: $content, author: $author) {
        id
        title
        content
        author
    }
}

```
  2. `updatePost`: Updates an existing post.
```
mutation UpdatePost($id: Int!, $title: String!, $content: String!, $author: String!) {
    updatePost(id: $id, title: $title, content: $content, author: $author) {
        id
        title
        content
        author
    }
}

```
  3. `deletePost`: Deletes a post by ID.
```
mutation DeletePost($id: Int!) {
    deletePost(id: $id)
}

```
## Testing the API
  1. Postman:
    - Use the deployed API URL: [http://45.137.148.234:8083/](http://45.137.148.234:8083/).
  2. React App:
  - Clone the React repository for a front-end interface:
```
git clone https://github.com/B-Yahia/ReactJS-GraphQL.git
cd ReactJS-GraphQL
```
  3. You can try the deployment of the [frontend](https://blog-ql-1.netlify.app/)
Update the API URL in src/GraphQL/apolloClient.js to point to the GraphQL server URL.
## Deployment

The GraphQL API is deployed on my VPS and accessible at:
[http://45.137.148.234:8083/](http://45.137.148.234:8083/)

## Configuring a Virtual Host

If you need to configure a virtual host to deploy this application, you can follow the instructions provided in the README file of the [linux-apache-virtual-host-setup](https://github.com/B-Yahia/linux-apache-virtual-host-setup) repository.

## Requirements

  - PHP 7.4+
  - MySQL
  - Composer
