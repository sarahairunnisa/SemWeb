<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty - Home</title>
    
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/c3c1353c4c.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- Connector untuk menghubungkan PHP dan SPARQL -->
    <?php
        require_once("sparqllib.php");
        $searchInput = "" ;
        
        if (isset($_POST['search'])) {
            $searchInput = $_POST['search'];
            $productData = sparql_get(
            "http://localhost:3030/sociolla",
            "
                PREFIX sociolla: <http://www.semantic.org/ontologies/sociolla#>
                SELECT (STR(?id) AS ?productId) (STR(?productName) AS ?productNameStr) (STR(?price) AS ?priceStr) (STR(?brandName) AS ?brandNameStr) (STR(?categoryName) AS ?categoryNameStr)
                WHERE {
                    ?product a sociolla:Product ;
                             sociolla:id ?id ;
                             sociolla:productName ?productName ;
                             sociolla:price ?price ;
                             sociolla:belongsToBrand ?brand ;
                             sociolla:belongsToCategory ?category .
                  
                    ?brand sociolla:brandName ?brandName .
                    ?category sociolla:categoryName ?categoryName .
                    
                    FILTER (CONTAINS(LCASE(?productName), '$searchInput'))
                }
            "
            );
        } elseif (isset($_GET['category'])) {
            $selectedCategory = $_GET['category'];
            $productData = sparql_get(
                "http://localhost:3030/sociolla",
                "
                    PREFIX sociolla: <http://www.semantic.org/ontologies/sociolla#>
                    SELECT (STR(?id) AS ?productId) (STR(?productName) AS ?productNameStr) (STR(?price) AS ?priceStr) (STR(?brandName) AS ?brandNameStr)
                    WHERE {
                      ?product a sociolla:Product ;
                               sociolla:id ?id ;
                               sociolla:productName ?productName ;
                               sociolla:price ?price ;
                               sociolla:belongsToCategory ?category ;
                               sociolla:belongsToBrand ?brand.
                      
                      ?category sociolla:categoryName '$selectedCategory'.
                      ?brand sociolla:brandName ?brandName.
                    }
                "
                );
        } elseif (isset($_GET['brand'])) {
            $selectedBrand = $_GET['brand'];
            $productData = sparql_get(
                "http://localhost:3030/sociolla",
                "
                    PREFIX sociolla: <http://www.semantic.org/ontologies/sociolla#>
                    SELECT (STR(?id) AS ?productId) (STR(?productName) AS ?productNameStr) (STR(?price) AS ?priceStr) (STR(?categoryName) AS ?categoryNameStr)
                    WHERE {
                      ?product a sociolla:Product ;
                               sociolla:id ?id ;
                               sociolla:productName ?productName ;
                               sociolla:price ?price ;
                               sociolla:belongsToBrand ?brand ;
                               sociolla:belongsToCategory ?category .
                      
                      ?brand sociolla:brandName '$selectedBrand' .
                      ?category sociolla:categoryName ?categoryName .
                    }
                "
                );
        }else {
            $productData = sparql_get(
            "http://localhost:3030/sociolla",
            "
                PREFIX sociolla: <http://www.semantic.org/ontologies/sociolla#>
                SELECT (STR(?id) AS ?productId) (STR(?productName) AS ?productNameStr) (STR(?price) AS ?priceStr) (STR(?brandName) AS ?brandNameStr) (STR(?categoryName) AS ?categoryNameStr)
                WHERE {
                    ?product a sociolla:Product ;
                             sociolla:id ?id ;
                             sociolla:productName ?productName ;
                             sociolla:price ?price ;
                             sociolla:belongsToBrand ?brand ;
                             sociolla:belongsToCategory ?category .
                  
                    ?brand sociolla:brandName ?brandName .
                    ?category sociolla:categoryName ?categoryName .
                }
            "
            );
        }

        $categoryData = sparql_get(
            "http://localhost:3030/sociolla",
            "
                PREFIX sociolla: <http://www.semantic.org/ontologies/sociolla#>
                SELECT (STR(?category_id) AS ?categoryId) (STR(?categoryName) AS ?categoryNameStr)
                WHERE {
                  ?category a sociolla:Category ;
                            sociolla:category_id ?category_id ;
                            sociolla:categoryName ?categoryName .
                }
                ORDER BY ASC(?categoryNameStr)
            "
        );

        $brandData = sparql_get(
            "http://localhost:3030/sociolla",
            "
                PREFIX sociolla: <http://www.semantic.org/ontologies/sociolla#>
                SELECT (STR(?brand_id) AS ?brandId) (STR(?brandName) AS ?brandNameStr)
                WHERE {
                  ?brand a sociolla:Brand ;
                         sociolla:brand_id ?brand_id ;
                         sociolla:brandName ?brandName .
                }
                ORDER BY ASC(?brandNameStr)
            "
        );

        if (!isset($productData) || !isset($categoryData) || !isset($brandData)) {
            print "<p>Error: " . sparql_errno() . ": " . sparql_error() . "</p>";
        }
    ?>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-danger bg-gradien sticky-top">
        <div class="container container-fluid">
            <a class="navbar-brand" href="index.php"><img src="src/img/beauty.png" style="width:90px" alt="Logo"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 h5">
                    <li class="nav-item px-2">
                        <a class="nav-link active text-white" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item dropdown px-2">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Search by Categories
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="max-height: 200px; overflow-y: auto;">
                            <?php foreach ($categoryData as $data) : ?>
                                <li><a class="dropdown-item" href="?category=<?= urlencode($data['categoryNameStr']) ?>"><?= htmlspecialchars($data['categoryNameStr']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-item dropdown px-2">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Search by Brand
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="max-height: 200px; overflow-y: auto;">
                            <?php foreach ($brandData as $data) : ?>
                                <li><a class="dropdown-item" href="?brand=<?= urlencode($data['brandNameStr']) ?>"><?= htmlspecialchars($data['brandNameStr']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
                <form class="d-flex" role="search" action="" method="post" id="search" name="search">
                    <input class="form-control me-2" type="search" placeholder="Cari Produk Disini" aria-label="Search" name="search">
                    <button class="btn btn-outline-light" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Body -->
    <div class="container container-fluid my-3">
        <?php
            if ($searchInput != NULL) {
                ?> 
                    <i class="fa-solid fa-magnifying-glass"></i><span>Menampilkan hasil pencarian untuk <b>"<?php echo $searchInput; ?>"</b></span> 
                <?php
            }
        ?>
        <!-- <h2>Categories</h2>
        <table class="table table-bordered table-hover text-center table-responsive">
            <thead class="table-dark align-middle">
                <tr>
                    <th>No.</th>
                    <th>Category ID</th>
                    <th>Category Name</th>
                </tr>
            </thead>
            <tbody class="align-middle">
                <?php $i = 0; ?>
                <?php foreach ($categoryData as $data) : ?>
                <tr>
                    <td><?= ++$i ?></td>
                    <td><?= $data['categoryId'] ?></td>
                    <td><?= $data['categoryNameStr'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table> -->

        <!-- <h2>Brands</h2>
        <table class="table table-bordered table-hover text-center table-responsive">
            <thead class="table-dark align-middle">
                <tr>
                    <th>No.</th>
                    <th>Brand ID</th>
                    <th>Brand Name</th>
                </tr>
            </thead>
            <tbody class="align-middle">
                <?php $i = 0; ?>
                <?php foreach ($brandData as $data) : ?>
                <tr>
                    <td><?= ++$i ?></td>
                    <td><?= $data['brandId'] ?></td>
                    <td><?= $data['brandNameStr'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table> -->
        <?php
        $productArray = json_decode(json_encode($productData), true);
        $perPage = 50;

        // Hitung total halaman
        $totalData = count($productArray);
        $totalPages = ceil($totalData / $perPage);

        // Dapatkan halaman saat ini dari URL, jika tidak ada default ke 1
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($current_page < 1) {
            $current_page = 1;
        } elseif ($current_page > $totalPages) {
            $current_page = $totalPages;
        }

        // Hitung offset untuk query
        $offset = ($current_page - 1) * $perPage;

        // Ambil data untuk halaman saat ini
        $currentData = array_slice($productArray, $offset, $perPage);
        ?>
        <h2>Products</h2>
        <table class="table table-bordered table-hover text-center table-responsive">
            <thead class="table-danger align-middle">
                <tr>
                    <th>No.</th>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <?php if (!isset($_GET['brand'])): ?>
                        <th>Brand</th>
                    <?php endif; ?>
                    <?php if (!isset($_GET['category'])): ?>
                        <th>Category</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="align-middle">
                <?php $i = $offset; ?>
                <?php foreach ($currentData as $data) : ?>
                <tr>
                    <td><?= ++$i ?></td>
                    <td><?= $data['productId'] ?></td>
                    <td><?= $data['productNameStr'] ?></td>
                    <td><?= $data['priceStr'] ?></td>
                    <?php if (!isset($_GET['brand']) && isset($data['brandNameStr'])): ?>
                        <td><?= htmlspecialchars($data['brandNameStr']) ?></td>
                    <?php endif; ?>
                    <?php if (!isset($_GET['category']) && isset($data['categoryNameStr'])): ?>
                        <td><?= htmlspecialchars($data['categoryNameStr']) ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            <nav>
                <ul class="pagination">
                    <?php if ($current_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $current_page - 1 ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                    <li class="page-item <?= ($page == $current_page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $page ?>"><?= $page ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($current_page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $current_page + 1 ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Footer -->
    <?php
        if ($searchInput != NULL) {
            ?> 
                <footer class="footer text-light text-center bg-danger bg-gradien pb-1 fixed-bottom">
                    <p>Copyright &copy; All rights reserved -<img src="src/img/beauty.png" style="width:75px" alt="Logo"></p>
                </footer>
            <?php
        } else {
            ?>
                <footer class="footer text-light text-center bg-danger bg-gradien pb-1">
                    <p>Copyright &copy; All rights reserved -<img src="src/img/beauty.png" style="width:75px" alt="Logo"></p>
                </footer>
            <?php
        }
    ?>
</body>
</html>