# -MOVIE-NIGHT-PLANNER

Movie Night Planner is a simple web application developed using **HTML, CSS, JavaScript, PHP, and MySQL**. It allows users to create and manage their personal movie watchlist. The project was deployed using **InfinityFree**, which provides free web hosting with PHP and MySQL support.

## Features

- Add a new movie.
- Select a movie genre.
- Rate movies from 1 to 5 stars.
- Add personal notes.
- Change the movie status (Watched / Not Watched) using a **Toggle** button.
- Update the movie status instantly without refreshing the page.
- Store and retrieve movie data from a MySQL database.

## Technologies Used

- HTML5
- CSS3
- JavaScript (AJAX / Fetch API)
- PHP
- MySQL
- InfinityFree (Web Hosting)

## Database

The project uses a MySQL table named **movies** with the following fields:

- id
- movie_name
- genre
- rating
- status
- created_at

## Project Files

```
index.html
index.php
toggle.php
README.md
```

## How the Project Works

1. The user enters movie information through the form.
2. PHP processes the submitted data.
3. The data is stored in a MySQL database.
4. All saved movies are displayed in a table.
5. Clicking the **Toggle** button changes the movie status and updates the database instantly without reloading the page using JavaScript and PHP.

## Deployment

The project was deployed on **InfinityFree**. A MySQL database was created, the application was connected to the database, and the project files were uploaded to the hosting server to test all functionalities.

## Learning Outcomes

- Designing responsive web interfaces using HTML and CSS.
- Connecting PHP with a MySQL database.
- Performing Create, Read, and Update (CRUD) operations.
- Using AJAX to update data without refreshing the page.
- Deploying a PHP web application on InfinityFree.
```
