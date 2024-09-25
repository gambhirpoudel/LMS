<?php
// Include the database connection
include('../includes/db.php');

// Fetch all transactions from the database
$query = "SELECT transactions.sale_id, transactions.price, books.name AS book_name, users.username AS user_name
          FROM transactions
          JOIN books ON transactions.book_id = books.id
          JOIN users ON transactions.user_id = users.id
          ORDER BY transactions.sale_id";
$stmt = $pdo->prepare($query);
$stmt->execute();
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Extract relevant fields
$sale_ids = array_column($sales, 'sale_id');
$sale_prices = array_column($sales, 'price');
$book_names = array_column($sales, 'book_name');
$user_names = array_column($sales, 'user_name');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sale Search</title>
    <link rel="stylesheet" href="./css/styles.css">
    <script>
        let saleIds = <?php echo json_encode($sale_ids); ?>;
        let salePrices = <?php echo json_encode($sale_prices); ?>;
        let bookNames = <?php echo json_encode($book_names); ?>;
        let userNames = <?php echo json_encode($user_names); ?>;

        function searchSales(query) {
            const results = [];
            for (let i = 0; i < saleIds.length; i++) {
                if (saleIds[i].toLowerCase().includes(query.toLowerCase())) {
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
                const indices = searchSales(searchQuery);

                if (indices.length > 0) {
                    indices.forEach(index => {
                        searchResults.innerHTML += `
                            <a href="./sales_details.php?sale_id=${saleIds[index]}" style="text-decoration: none;">
                                <div class="search-item">
                                    ${bookNames[index]}
                                </div>
                            </a>`;
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


    <div class="search-container">
        <input type="text" class="search" placeholder="Search by Sale ID" onkeyup="liveSearch()" id="searchBar" autocomplete="off" />
    </div>
    <div id="searchResults" class="search-results" style="display:none;"></div>


</body>

</html>