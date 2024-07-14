// Assume $menuItems is fetched from the database or XML file
$menuItems = [
    'Coffee' => ['Espresso', 'Americano', 'Latte'],
    'Pastry' => ['Croissant', 'Muffin', 'Scone'],
    // Add more categories and items as per your menu
];

// JavaScript for dynamic UI interactions
// Use AJAX to fetch this data and update the UI accordingly

echo "<div id='menuSelection'>";
foreach ($menuItems as $category => $items) {
    echo "<h3>$category</h3><ul>";
    foreach ($items as $item) {
        // Display each item with an option to specify quantity
        echo "<li>$item - Quantity: <input type='number' name='quantity[$item]' min='1' max='10' value='1'></li>";
    }
    echo "</ul>";
}
echo "</div>";
