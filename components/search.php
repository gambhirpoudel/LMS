<?php
// Include the database connection
include './includes/db.php';

// Fetch all books from the database
$query = "SELECT * FROM books ORDER BY name";
$stmt = $pdo->prepare($query);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Extract relevant fields
$book_names = array_column($books, 'name');
$book_authors = array_column($books, 'author');
$book_isbns = array_column($books, 'isbn');
$book_ids = array_column($books, 'id');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Book Search</title>
    <script>
        let bookNames = <?php echo json_encode($book_names); ?>;
        let bookAuthors = <?php echo json_encode($book_authors); ?>;
        let bookIsbns = <?php echo json_encode($book_isbns); ?>;
        let bookIds = <?php echo json_encode($book_ids); ?>;

        function searchAll(query) {
            const results = [];
            for (let i = 0; i < bookNames.length; i++) {
                if (bookNames[i].toLowerCase().includes(query.toLowerCase()) ||
                    bookAuthors[i].toLowerCase().includes(query.toLowerCase()) ||
                    bookIsbns[i].toLowerCase().includes(query.toLowerCase())) {
                    results.push(i);
                }
            }
            return results;
        }

        function liveSearch() {
            const searchQuery = document.getElementById('searchBar').value;
            const searchResults = document.getElementById('searchResults');
            searchResults.innerHTML = '';

            if (searchQuery.length > 0) {
                const indices = searchAll(searchQuery);

                if (indices.length > 0) {
                    indices.forEach(index => {
                        searchResults.innerHTML += '<a href="./book_details.php?id=' + bookIds[index] + '" style="text-decoration: none;"><div class="search-item">' + bookNames[index] + '</div></a>';
                    });
                } else {
                    searchResults.innerHTML = '<p>No results found</p>';
                }
                searchResults.style.display = 'block';
            } else {
                searchResults.style.display = 'none';
            }
        }

        document.addEventListener('click', function(event) {
            const searchBar = document.getElementById('searchBar');
            const searchResults = document.getElementById('searchResults');

            if (!searchBar.contains(event.target) && !searchResults.contains(event.target)) {
                searchResults.style.display = 'none';
            }
        });
    </script>
</head>

<body>
    <ul>
        <li class="search-box">
            <div class="search-container">
                <input type="text" class="search" placeholder="Search" onkeyup="liveSearch()" id="searchBar" autocomplete="off" />
                <div class="search-icon"><i class="fa fa-search"></i></div>
            </div>
            <div id="searchResults" class="search-results" style="display:none;"></div>
        </li>
    </ul>
</body>

</html>