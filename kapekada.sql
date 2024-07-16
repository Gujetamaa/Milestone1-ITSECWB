-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 16, 2024 at 05:57 PM
-- Server version: 8.0.37
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kapekada`
--

-- --------------------------------------------------------

--
-- Table structure for table `combo_meals`
--

CREATE TABLE `combo_meals` (
  `combo_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `main_dish` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `side_dish` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `drink` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `combo_meals`
--

INSERT INTO `combo_meals` (`combo_id`, `name`, `description`, `main_dish`, `side_dish`, `drink`, `price`, `discount_percentage`, `category`, `quantity`) VALUES
(100, 'Cheesy Potato Delight Combo', 'Enjoy the indulgent flavors of our Ultimate Cheese Bagel, paired with a Baked Potato, and complemented by an Iced Chocolate.', 'Ultimate Cheese Bagel', 'Baked Potato', 'Iced Chocolate', 250.00, 15.00, 'Morning', 47),
(101, 'Mediterranean Wedges Bliss Combo', 'Savor the Mediterranean taste with our Italian Grilled Bagel, accompanied by Steamed Vegetables, and served with refreshing Water.', 'Italian Grilled Bagel', 'Steamed Vegetables', 'Water', 300.00, 10.00, 'Evening', 50),
(102, 'Morning Energy Boost Combo', 'Start your day with energy! Our Morning Energy Boost Combo includes Pancake, Egg Toast, and a refreshing Coke.', 'Breakfast Wrap', 'Crisscut Fries', 'Chocolate Cream Frappuccino', 200.00, 10.00, 'Morning', 50),
(103, 'Evening Delight Combo', 'Experience an evening delight with our Grilled Chicken Wrap, served with crispy French Fries and a delightful Mocha Frappe.', 'Grilled Chicken Wrap', 'French Fries', 'Mocha Frappe', 280.00, 10.00, 'Evening', 50),
(104, 'Aprils Specials', 'Enjoy the specials', 'Meatball Pasta', 'Crisscut Fries', 'Iced Chocolate', 199.00, 10.00, 'Morning', 20);

--
-- Triggers `combo_meals`
--
DELIMITER $$
CREATE TRIGGER `combo_meals_AFTER_INSERT` AFTER INSERT ON `combo_meals` FOR EACH ROW BEGIN

    INSERT INTO combo_meals_audit (audit_timestamp, activity, combo_id,
        new_name, new_description, new_main_dish, new_side_dish, new_drink, new_price,
        new_discount_percentage, new_category, new_quantity)
    VALUES (NOW(), 'C', new.combo_id,
        new.name, new.description, new.main_dish, new.side_dish, new.drink, new.price,
        new.discount_percentage, new.category, new.quantity);

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `combo_meals_AFTER_UPDATE` AFTER UPDATE ON `combo_meals` FOR EACH ROW BEGIN

    INSERT INTO combo_meals_audit (audit_timestamp, activity, combo_id,
        old_name, old_description, old_main_dish, old_side_dish, old_drink, old_price,
        old_discount_percentage, old_category, old_quantity,
        new_name, new_description, new_main_dish, new_side_dish, new_drink, new_price,
        new_discount_percentage, new_category, new_quantity)
    VALUES (NOW(), 'U', old.combo_id,
        old.name, old.description, old.main_dish, old.side_dish, old.drink, old.price,
        old.discount_percentage, old.category, old.quantity,
        new.name, new.description, new.main_dish, new.side_dish, new.drink, new.price,
        new.discount_percentage, new.category, new.quantity);


END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `combo_meals_BEFORE_DELETE` BEFORE DELETE ON `combo_meals` FOR EACH ROW BEGIN

    INSERT INTO combo_meals_audit (audit_timestamp, activity, combo_id,
        old_name, old_description, old_main_dish, old_side_dish, old_drink, old_price,
        old_discount_percentage, old_category, old_quantity)
    VALUES (NOW(), 'U', OLD.combo_id,
        old.name, old.description, old.main_dish, old.side_dish, old.drink, old.price,
        old.discount_percentage, old.category, old.quantity);


END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `combo_meals_BEFORE_INSERT` BEFORE INSERT ON `combo_meals` FOR EACH ROW BEGIN

    DECLARE max_combo INT DEFAULT 0;

    SELECT MAX(combo_id) INTO max_combo FROM combo_meals;
    
    IF max_combo IS NULL THEN
        SET new.combo_id = 1;
    ELSE
        SET new.combo_id = max_combo + 1;
    END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `combo_meals_audit`
--

CREATE TABLE `combo_meals_audit` (
  `audit_id` int NOT NULL,
  `audit_timestamp` datetime NOT NULL,
  `activity` enum('C','U','D') COLLATE utf8mb4_general_ci NOT NULL,
  `combo_id` int NOT NULL,
  `old_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_description` text COLLATE utf8mb4_general_ci,
  `old_main_dish` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_side_dish` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_drink` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `old_discount_percentage` decimal(5,2) DEFAULT NULL,
  `old_category` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_quantity` int DEFAULT NULL,
  `new_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_description` text COLLATE utf8mb4_general_ci,
  `new_main_dish` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_side_dish` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_drink` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_price` decimal(10,2) DEFAULT NULL,
  `new_discount_percentage` decimal(5,2) DEFAULT NULL,
  `new_category` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_quantity` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `menu_item_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `stock_quantity` int NOT NULL,
  `image` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`menu_item_id`, `name`, `category`, `price`, `description`, `stock_quantity`, `image`) VALUES
(1, 'Ultimate Cheese Bagel', 'Mains', 180.00, 'A delicious bagel filled with a variety of cheeses.', 42, '660182133258c.webp'),
(2, 'Breakfast Wrap', 'Mains', 220.00, 'A hearty breakfast wrap filled with eggs, bacon, and cheese.', 47, '660182324c8d0.webp'),
(3, 'Italian Grilled Bagel', 'Mains', 200.00, 'Grilled bagel with Italian seasoning and cheese.', 50, '6601825024b2f.webp'),
(4, 'Meatball Pasta', 'Mains', 240.00, 'Pasta served with homemade meatballs and marinara sauce.', 50, '6601826233d7c.webp'),
(5, 'Baked Potato', 'Sides', 120.00, 'Baked potato served with butter and sour cream.', 49, '6601827671d9b.webp'),
(6, 'Potato Wedges', 'Sides', 150.00, 'Crispy potato wedges seasoned to perfection.', 50, '66018293ad169.webp'),
(7, 'Crisscut Fries', 'Sides', 200.00, 'Crisscut fries with a crispy exterior and fluffy interior.', 50, '660182aa45198.webp'),
(8, 'Onion Rings', 'Sides', 130.00, 'Golden-brown onion rings served with dipping sauce.', 50, '660182c147c4e.webp'),
(9, 'Iced Chocolate', 'Drink', 150.00, 'Refreshing iced chocolate beverage.', 49, '660182db60f94.webp'),
(10, 'Iced/Hot Caramel Macchiato', 'Drink', 180.00, 'A perfect blend of espresso, vanilla, and caramel.', 50, '660182f46917c.webp'),
(11, 'Chocolate Cream Frappuccino', 'Drink', 210.00, 'Creamy chocolate frappuccino topped with whipped cream.', 50, '6601831029d96.webp'),
(12, 'Bottled Water', 'Drink', 25.00, 'Chilled bottled water.', 40, '6601833ee4cd3.webp');

--
-- Triggers `menu_items`
--
DELIMITER $$
CREATE TRIGGER `menu_items_AFTER_INSERT` AFTER INSERT ON `menu_items` FOR EACH ROW BEGIN

    INSERT INTO menu_items_audit (audit_timestamp, activity, menu_item_id,
        new_name, new_category, new_price, new_description, new_stock_quantity, new_image, end_user, end_reason)
    VALUES (NOW(), 'C', new.menu_item_id,
        new.name, new.category, new.price, new.description, new.stock_quantity, new.image, 
        "Administrator", "New Menu Item Created");


END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `menu_items_AFTER_UPDATE` AFTER UPDATE ON `menu_items` FOR EACH ROW BEGIN

    INSERT INTO menu_items_audit (audit_timestamp, activity, menu_item_id,
        old_name, old_category, old_price, old_description, old_stock_quantity, old_image,
        new_name, new_category, new_price, new_description, new_stock_quantity, new_image, end_user, end_reason)
    VALUES (NOW(), 'U', old.menu_item_id,
        old.name, old.category, old.price, old.description, old.stock_quantity, old.image,
        new.name, new.category, new.price, new.description, new.stock_quantity, new.image, "Administrator", "Updated Menu Item");


END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `menu_items_BEFORE_DELETE` BEFORE DELETE ON `menu_items` FOR EACH ROW BEGIN

    INSERT INTO menu_items_audit (audit_timestamp, activity, menu_item_id,
        old_name, old_category, old_price, old_description, old_stock_quantity, old_image, 
        end_user, end_reason)
    VALUES (NOW(), 'D', old.menu_item_id,
        old.name, old.category, old.price, old.description, old.stock_quantity, old.image, 
        "Administrator", "Deleted menu item");


END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `menu_items_BEFORE_INSERT` BEFORE INSERT ON `menu_items` FOR EACH ROW BEGIN

    DECLARE max_menu INT DEFAULT 0;

    SELECT MAX(menu_item_id) INTO max_menu FROM menu_items;
    
    IF max_menu IS NULL THEN
        SET new.menu_item_id = 1;
    ELSE
        SET new.menu_item_id = max_menu + 1;
    END IF;



END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items_audit`
--

CREATE TABLE `menu_items_audit` (
  `audit_id` int NOT NULL,
  `audit_timestamp` datetime NOT NULL,
  `activity` enum('C','U','D') COLLATE utf8mb4_general_ci NOT NULL,
  `menu_item_id` int NOT NULL,
  `old_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_category` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `old_description` text COLLATE utf8mb4_general_ci,
  `old_stock_quantity` int DEFAULT NULL,
  `old_image` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_category` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_price` decimal(10,2) DEFAULT NULL,
  `new_description` text COLLATE utf8mb4_general_ci,
  `new_stock_quantity` int DEFAULT NULL,
  `new_image` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `end_user` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `end_reason` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items_audit`
--

INSERT INTO `menu_items_audit` (`audit_id`, `audit_timestamp`, `activity`, `menu_item_id`, `old_name`, `old_category`, `old_price`, `old_description`, `old_stock_quantity`, `old_image`, `new_name`, `new_category`, `new_price`, `new_description`, `new_stock_quantity`, `new_image`, `end_user`, `end_reason`) VALUES
(1, '2024-07-16 11:46:16', 'U', 2, 'Breakfast Wrap', 'Mains', 220.00, 'A hearty breakfast wrap filled with eggs, bacon, and cheese.', 48, '660182324c8d0.webp', 'Breakfast Wrap', 'Mains', 220.00, 'A hearty breakfast wrap filled with eggs, bacon, and cheese.', 47, '660182324c8d0.webp', 'Administrator', 'Updated Menu Item'),
(2, '2024-07-16 23:55:20', 'U', 1, 'Ultimate Cheese Bagel', 'Mains', 180.00, 'A delicious bagel filled with a variety of cheeses.', 43, '660182133258c.webp', 'Ultimate Cheese Bagel', 'Mains', 180.00, 'A delicious bagel filled with a variety of cheeses.', 42, '660182133258c.webp', 'Administrator', 'Updated Menu Item');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `user_id` int NOT NULL,
  `order_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_price` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `customer_address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `total_price`, `discount_amount`, `quantity`, `customer_address`) VALUES
(2, 1, '2024-04-04 00:00:00', 100.00, 450.00, 1, '1411 taft'),
(3, 1, '2020-01-01 00:00:00', 150.00, 150.00, 5, '1411 taft'),
(4, 1, '2024-06-23 15:59:12', 500.00, 10.00, 6, '1411 taft'),
(5, 122, '2024-07-16 23:55:20', 180.00, 0.00, 1, '1411 taft');

--
-- Triggers `orders`
--
DELIMITER $$
CREATE TRIGGER `orders_AFTER_DELETE` AFTER DELETE ON `orders` FOR EACH ROW BEGIN

INSERT INTO orders_audit (order_id, user_id, order_date,audit_timestamp,activity,
 old_total_price, old_discount_amount, old_quantity, old_customerAddress,end_user,end_reason) 
 VALUES (old.order_id, old.user_id, old.order_date, NOW(),'D', old.total_price, old.discount_amount, 
 old.quantity, old.customer_address, 'Administrator', 'record deletion');

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `orders_AFTER_INSERT` AFTER INSERT ON `orders` FOR EACH ROW BEGIN

	INSERT INTO orders_audit (order_id, user_id, order_date, audit_timestamp, activity, 
	new_total_price, new_discount_amount, new_quantity, new_customerAddress, end_user, end_reason) 
	VALUES (new.order_id, new.user_id, new.order_date, NOW(),'C',  new.total_price, new.discount_amount, 
	new.quantity, new.customer_address, 'Administrator', 'new order created');

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `orders_AFTER_UPDATE` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN

	INSERT INTO orders_audit (order_id, user_id, order_date, audit_timestamp, activity,
	old_total_price, old_discount_amount, old_quantity, old_customerAddress, new_total_price, 
	new_discount_amount, new_quantity, new_customerAddress, end_user, end_reason) 
	VALUES (old.order_id, old.user_id, old.order_date, NOW(),'U', old.total_price, old.discount_amount, 
	old.quantity, old.customer_address, new.total_price, new.discount_amount, 
	new.quantity, new.customer_address, 'Administrator', 'record deletion');


END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `orders_BEFORE_INSERT` BEFORE INSERT ON `orders` FOR EACH ROW BEGIN

    DECLARE max_order INT DEFAULT 0;

    SELECT MAX(order_id) INTO max_order FROM orders;
    
    IF max_order IS NULL THEN
        SET new.order_id = 1;
    ELSE
        SET new.order_id = max_order + 1;
    END IF;
    
    SET new.order_date = NOW();


END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `orders_audit`
--

CREATE TABLE `orders_audit` (
  `audit_id` int NOT NULL,
  `audit_timestamp` datetime NOT NULL,
  `activity` enum('C','U','D') NOT NULL,
  `order_id` int NOT NULL,
  `user_id` int NOT NULL,
  `order_date` datetime NOT NULL,
  `old_total_price` decimal(10,2) DEFAULT NULL,
  `old_discount_amount` decimal(10,2) DEFAULT NULL,
  `old_quantity` int DEFAULT NULL,
  `old_customerAddress` varchar(255) DEFAULT NULL,
  `new_total_price` decimal(10,2) DEFAULT NULL,
  `new_discount_amount` decimal(10,2) DEFAULT NULL,
  `new_quantity` int DEFAULT NULL,
  `new_customerAddress` varchar(255) DEFAULT NULL,
  `end_user` varchar(45) DEFAULT NULL,
  `end_reason` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders_audit`
--

INSERT INTO `orders_audit` (`audit_id`, `audit_timestamp`, `activity`, `order_id`, `user_id`, `order_date`, `old_total_price`, `old_discount_amount`, `old_quantity`, `old_customerAddress`, `new_total_price`, `new_discount_amount`, `new_quantity`, `new_customerAddress`, `end_user`, `end_reason`) VALUES
(1, '2024-07-16 23:55:20', 'C', 5, 122, '2024-07-16 23:55:20', NULL, NULL, NULL, NULL, 180.00, 0.00, 1, '1411 taft', 'Administrator', 'new order created');

-- --------------------------------------------------------

--
-- Table structure for table `specials`
--

CREATE TABLE `specials` (
  `specials_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `specials`
--

INSERT INTO `specials` (`specials_id`, `name`, `description`, `price`, `start_date`, `end_date`) VALUES
(1, 'Happy Hour Special', 'Enjoy discounted prices on selected drinks during happy hour.', 199.99, '2024-03-18', '2024-03-31'),
(65, 'Weekend Brunch Deal', 'Indulge in our special brunch menu at a discounted price every weekend.', 299.99, '2024-03-20', '2024-03-26'),
(66, 'Lunch Combo Special', 'Get a combo meal for a great price during lunch hours on weekdays.', 399.49, '2024-03-21', '2024-03-25');

--
-- Triggers `specials`
--
DELIMITER $$
CREATE TRIGGER `specials_AFTER_INSERT` AFTER INSERT ON `specials` FOR EACH ROW BEGIN
    INSERT INTO specials_audit (specials_id, audit_timestamp, activity, new_name, 
    new_description, new_price, new_startdate, new_enddate, end_user, end_reason) 
	VALUES (new.specials_id, NOW(), 'C', new.name, new.description, new.price, new.start_date, 
	new.end_date, 'Administrator', 'New special created');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `specials_AFTER_UPDATE` AFTER UPDATE ON `specials` FOR EACH ROW BEGIN

INSERT INTO specials_audit (specials_id, audit_timestamp, activity, old_name, 
    old_description, old_price, old_startdate, old_enddate, new_name, 
    new_description, new_price, new_startdate, new_enddate, end_user, end_reason) 
    VALUES (old.specials_id, NOW(), 'U', old.name, old.description, old.price, old.start_date, 
	old.end_date, new.name, new.description, new.price, new.start_date, 
	new.end_date, 'Administrator', 'updated information of this special');


END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `specials_BEFORE_DELETE` BEFORE DELETE ON `specials` FOR EACH ROW BEGIN

    INSERT INTO specials_audit (specials_id, audit_timestamp, activity, old_name, 
        old_description, old_price, old_startdate, old_enddate, end_user, end_reason) 
	VALUES (
        old.specials_id, NOW(), 'D', old.name, old.description, old.price, old.start_date, old.end_date, 
        'Administrator','Deleted this special');

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `specials_BEFORE_INSERT` BEFORE INSERT ON `specials` FOR EACH ROW BEGIN

    DECLARE max_specials INT DEFAULT 0;

    SELECT MAX(specials_id) INTO max_specials FROM specials;
    
    IF max_specials IS NULL THEN
        SET new.specials_id = 1;
    ELSE
        SET new.specials_id = max_specials + 1;
    END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `specials_audit`
--

CREATE TABLE `specials_audit` (
  `audit_id` int NOT NULL,
  `audit_timestamp` datetime NOT NULL,
  `activity` enum('C','U','D') COLLATE utf8mb4_general_ci NOT NULL,
  `specials_id` int NOT NULL,
  `old_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_description` text COLLATE utf8mb4_general_ci,
  `old_price` decimal(8,2) DEFAULT NULL,
  `old_startdate` date DEFAULT NULL,
  `old_enddate` date DEFAULT NULL,
  `new_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_description` text COLLATE utf8mb4_general_ci,
  `new_price` decimal(8,2) DEFAULT NULL,
  `new_startdate` date DEFAULT NULL,
  `new_enddate` date DEFAULT NULL,
  `end_user` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `end_reason` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `specials_audit`
--

INSERT INTO `specials_audit` (`audit_id`, `audit_timestamp`, `activity`, `specials_id`, `old_name`, `old_description`, `old_price`, `old_startdate`, `old_enddate`, `new_name`, `new_description`, `new_price`, `new_startdate`, `new_enddate`, `end_user`, `end_reason`) VALUES
(1, '2024-06-23 15:21:05', 'D', 1, 'Hotdog Special ni Player', 'all in kay banker', 500.00, '2024-06-06', '2024-07-06', NULL, NULL, NULL, NULL, NULL, 'Administrator', 'Deleted this special'),
(2, '2024-06-23 15:21:39', 'C', 2, NULL, NULL, NULL, NULL, NULL, 'hotdog bun', 'hotdog bun', 40.00, '2024-05-01', '2024-05-05', 'Administrator', 'New special created'),
(3, '2024-06-23 15:22:07', 'U', 2, 'hotdog bun', 'hotdog bun', 40.00, '2024-05-01', '2024-05-05', NULL, NULL, NULL, NULL, NULL, 'Administrator', 'old information of this special before update'),
(4, '2024-06-23 15:22:07', 'U', 2, NULL, NULL, NULL, NULL, NULL, 'burger bun', 'hotdog bun', 70.00, '2024-05-01', '2024-05-05', 'Administrator', 'updated information of this special'),
(5, '2024-06-23 15:24:17', 'C', 3, NULL, NULL, NULL, NULL, NULL, 'hotdog ni banker', 'all in kay banker', 5000.00, '2024-06-06', '2024-06-07', 'Administrator', 'New special created'),
(6, '2024-06-23 15:24:34', 'D', 2, 'burger bun', 'hotdog bun', 70.00, '2024-05-01', '2024-05-05', NULL, NULL, NULL, NULL, NULL, 'Administrator', 'Deleted this special');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(330) COLLATE utf8mb4_general_ci NOT NULL,
  `phoneNumber` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `birthday` date NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `wallet` decimal(10,2) DEFAULT '0.00',
  `address` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `picture` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `login_attempts` int DEFAULT NULL,
  `ban_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fullname`, `email`, `phoneNumber`, `birthday`, `password`, `role`, `wallet`, `address`, `picture`, `login_attempts`, `ban_time`) VALUES
(2, 'John Doe', 'john.doe@example.com', '9876543210', '0000-00-00', 'password123', 'User', 100.00, '456 Oak Street', '', NULL, NULL),
(54, 'Jane Smith', 'jane.smith@example.com', '5551234567', '0000-00-00', 'password456', 'User', 50.00, '789 Maple Avenue', '', NULL, NULL),
(101, 'Michael Johnson', 'michael.johnson@example.com', '1112223333', '0000-00-00', 'password789', 'User', 200.00, '101 Pine Road', '', NULL, NULL),
(115, 'admin', 'admin@example.com', '09176861123', '0000-00-00', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL),
(116, 'justine anne', 'justine1@gmail.com', '09176860040', '0000-00-00', '$2y$10$ZqWYiEiKpWT3NLooLy/UzuTLxubfvbkJbEFZfhXuhJ0c/yU8vCKWy', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/293066135_768748097491834_8197535814127481849_n.jpg', 0, NULL),
(117, 'justine2', 'justine2@gmail.com', '09176860046', '0000-00-00', '$2y$10$elzJb1JNHMaxB6M6BWFiduO7EFmMGFxl3tvovKNmsYyuTwAEDOuJG', 'User', 100.00, 'manila', 'uploads/user.jpeg', NULL, NULL),
(118, 'justine3', 'justine3@gmail.com', '09176860046', '0000-00-00', '$2y$10$id/KB2vEO6Eh3BcogCF1Rea8RyNdveOXK5RGlpRJsvkzdfTRRcn5a', 'User', 100.00, 'manila', 'uploads/taylor.jpeg', 0, NULL),
(119, 'justine4', 'justine4@gmail.com', '09176860046', '0000-00-00', '$2y$10$p2sCHfPoIrcoKK/6QlwTuekaQvV4L8fEcNZ5EFGx3S/HaRJGYqzNW', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL),
(120, 'anne', 'anne@gmail.com', '09176860046', '2002-08-24', '$2y$10$sb.eS9A0ZBwJWsnh1A7rSe91XiUjimUNd6jtUhudY0nvmEy8Gd4va', 'User', 100.00, 'manila', 'uploads/293066135_768748097491834_8197535814127481849_n.jpg', NULL, NULL),
(121, 'test1hihi', 'test1@gmail.com', '09176860047', '2024-07-26', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 880.00, 'manila', 'uploads/user.jpeg', 0, NULL),
(122, 'Banker Joe', 'banker@mail.com', '09176566235', '2001-01-01', '$2y$10$OPLckX7Q2OL6VlG/zcocFOiMK5CnuQUYENbhQQgKTijY0dP8Cu4k6', 'User', 9640.00, '1411 taft', 'uploads/415919457_24688718220741925_2080994886690690471_n.jpg', 0, NULL);

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `users_AFTER_DELETE` AFTER DELETE ON `users` FOR EACH ROW BEGIN

    INSERT INTO users_audit (audit_timestamp, activity, user_id,
        old_fullname, old_email, old_phoneNumber, old_password, old_role, 
        old_wallet, old_address, old_picture, old_login_attempts, old_ban_time, end_user, end_reason)
     VALUES (NOW(), 'D', OLD.user_id,
        old.fullname, old.email, old.phoneNumber, old.password, old.role, 
        old.wallet, old.address, old.picture, old.login_attempts, old.ban_time, "System", "Deleted user");

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `users_AFTER_INSERT` AFTER INSERT ON `users` FOR EACH ROW BEGIN

    INSERT INTO users_audit (audit_timestamp, activity, user_id,
	new_fullname, new_email, new_phoneNumber, new_password, new_role, 
    new_wallet, new_address, new_picture, new_login_attempts, new_ban_time, 
    end_user, end_reason)
    VALUES (NOW(), 'C', new.user_id,
	new.fullname, new.email, new.phoneNumber, new.password, new.role, 
    new.wallet, new.address, new.picture, new.login_attempts, new.ban_time, 
    "System", "New user registered");

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `users_AFTER_UPDATE` AFTER UPDATE ON `users` FOR EACH ROW BEGIN

    INSERT INTO users_audit (audit_timestamp, activity, user_id,
        old_fullname, old_email, old_phoneNumber, old_password, old_role, old_wallet, old_address, old_picture, old_login_attempts, old_ban_time,
        new_fullname, new_email, new_phoneNumber, new_password, new_role, new_wallet, new_address, new_picture, new_login_attempts, new_ban_time, end_user, end_reason)
    VALUES (NOW(), 'U', OLD.user_id,
        old.fullname, old.email, old.phoneNumber, old.password, old.role, old.wallet, old.address, old.picture, old.login_attempts, old.ban_time,
        new.fullname, new.email, new.phoneNumber, new.password, new.role, new.wallet, new.address, new.picture, new.login_attempts, new.ban_time, "System", "Updated user details");



END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `users_BEFORE_INSERT` BEFORE INSERT ON `users` FOR EACH ROW BEGIN

    DECLARE max_user INT DEFAULT 0;

    SELECT MAX(user_id) INTO max_user FROM users;
    
    IF max_user IS NULL THEN
        SET new.user_id = 1;
    ELSE
        SET new.user_id = max_user + 1;
    END IF;
    
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users_audit`
--

CREATE TABLE `users_audit` (
  `audit_id` int NOT NULL,
  `audit_timestamp` datetime NOT NULL,
  `activity` enum('C','U','D') COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int NOT NULL,
  `old_fullname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_email` varchar(330) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_phoneNumber` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_role` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_wallet` decimal(10,2) DEFAULT NULL,
  `old_address` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_picture` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_login_attempts` int DEFAULT NULL,
  `old_ban_time` int DEFAULT NULL,
  `new_fullname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_email` varchar(330) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_phoneNumber` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_role` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_wallet` decimal(10,2) DEFAULT NULL,
  `new_address` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_picture` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_login_attempts` int DEFAULT NULL,
  `new_ban_time` int DEFAULT NULL,
  `end_user` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `end_reason` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_audit`
--

INSERT INTO `users_audit` (`audit_id`, `audit_timestamp`, `activity`, `user_id`, `old_fullname`, `old_email`, `old_phoneNumber`, `old_password`, `old_role`, `old_wallet`, `old_address`, `old_picture`, `old_login_attempts`, `old_ban_time`, `new_fullname`, `new_email`, `new_phoneNumber`, `new_password`, `new_role`, `new_wallet`, `new_address`, `new_picture`, `new_login_attempts`, `new_ban_time`, `end_user`, `end_reason`) VALUES
(1, '2024-07-13 16:35:50', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'System', 'Updated user details'),
(2, '2024-07-13 16:35:50', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'System', 'Updated user details'),
(3, '2024-07-16 01:11:18', 'C', 116, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'justine1', 'justine1@gmail.com', '09176860046', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay City', 'uploads/brown folder.png', NULL, NULL, 'System', 'New user registered'),
(4, '2024-07-16 01:11:31', 'U', 116, 'justine1', 'justine1@gmail.com', '09176860046', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay City', 'uploads/brown folder.png', NULL, NULL, 'justine1!!!!', 'justine1@gmail.com', '09176860046', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay City', 'uploads/brown folder.png', NULL, NULL, 'System', 'Updated user details'),
(5, '2024-07-16 01:11:54', 'U', 116, 'justine1!!!!', 'justine1@gmail.com', '09176860046', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay City', 'uploads/brown folder.png', NULL, NULL, 'justine1!!!!', 'justine1@gmail.com', '09176860046', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay City', 'uploads/brown folder.png', NULL, NULL, 'System', 'Updated user details'),
(6, '2024-07-16 01:13:26', 'U', 116, 'justine1!!!!', 'justine1@gmail.com', '09176860046', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay City', 'uploads/brown folder.png', NULL, NULL, 'justine1!!!!hihi', 'justine1@gmail.com', '09176860044', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy', 'uploads/brown folder.png', NULL, NULL, 'System', 'Updated user details'),
(7, '2024-07-16 01:13:32', 'U', 116, 'justine1!!!!hihi', 'justine1@gmail.com', '09176860044', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy', 'uploads/brown folder.png', NULL, NULL, 'justine1!!!!hihi', 'justine1@gmail.com', '09176860040', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy', 'uploads/brown folder.png', NULL, NULL, 'System', 'Updated user details'),
(8, '2024-07-16 01:14:37', 'U', 116, 'justine1!!!!hihi', 'justine1@gmail.com', '09176860040', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy', 'uploads/brown folder.png', NULL, NULL, 'justine1!!!!hihi', 'justine1@gmail.com', '09176860040', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy', 'uploads/brown folder.png', 1, NULL, 'System', 'Updated user details'),
(9, '2024-07-16 01:14:37', 'U', 116, 'justine1!!!!hihi', 'justine1@gmail.com', '09176860040', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy', 'uploads/brown folder.png', 1, NULL, 'justine1!!!!hihi', 'justine1@gmail.com', '09176860040', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy', 'uploads/brown folder.png', 0, NULL, 'System', 'Updated user details'),
(10, '2024-07-16 01:14:55', 'U', 116, 'justine1!!!!hihi', 'justine1@gmail.com', '09176860040', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy', 'uploads/brown folder.png', 0, NULL, 'justine1!!!!hihi', 'justine1@gmail.com', '09176860046', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(11, '2024-07-16 01:15:06', 'U', 116, 'justine1!!!!hihi', 'justine1@gmail.com', '09176860046', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey', 'uploads/taylor.jpeg', 0, NULL, 'justine1!!!!hihi', 'justine1@gmail.com', '09176860046', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(12, '2024-07-16 01:18:53', 'U', 116, 'justine1!!!!hihi', 'justine1@gmail.com', '09176860046', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey', 'uploads/taylor.jpeg', 0, NULL, 'justine1!!!!hihi', 'justine1!!@gmail.com', '09176860046', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(13, '2024-07-16 01:19:10', 'U', 116, 'justine1!!!!hihi', 'justine1!!@gmail.com', '09176860046', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey', 'uploads/taylor.jpeg', 0, NULL, 'justine1!!!!hihi', 'justine1!!@gmail.com', '09176860040', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey yessir', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(14, '2024-07-16 01:24:18', 'U', 116, 'justine1!!!!hihi', 'justine1!!@gmail.com', '09176860040', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey yessir', 'uploads/user.jpeg', 0, NULL, 'justine1!!!!hihi', 'justine1@gmail.com', '09176860040', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(15, '2024-07-16 01:26:40', 'U', 116, 'justine1!!!!hihi', 'justine1@gmail.com', '09176860040', '$2y$10$uMv2wBm8KsSiWywVwK5xmOkZFE6FYD56tjxW8H4d/Vwau3c/R9MQS', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'justine1!!!!hihi', 'justine1@gmail.com', '09176860040', '$2y$10$bc1gs2JGjNtC3BywzrQj1O/IeVAPoqqQwXmrDrHzKls.m3OZv/yBG', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(16, '2024-07-16 01:26:47', 'U', 116, 'justine1!!!!hihi', 'justine1@gmail.com', '09176860040', '$2y$10$bc1gs2JGjNtC3BywzrQj1O/IeVAPoqqQwXmrDrHzKls.m3OZv/yBG', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$bc1gs2JGjNtC3BywzrQj1O/IeVAPoqqQwXmrDrHzKls.m3OZv/yBG', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(17, '2024-07-16 01:26:58', 'U', 116, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$bc1gs2JGjNtC3BywzrQj1O/IeVAPoqqQwXmrDrHzKls.m3OZv/yBG', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(18, '2024-07-16 01:42:34', 'U', 116, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 1, NULL, 'System', 'Updated user details'),
(19, '2024-07-16 01:42:34', 'U', 116, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 1, NULL, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(20, '2024-07-16 01:51:26', 'C', 117, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'justine2', 'justine2@gmail.com', '09176860046', '$2y$10$elzJb1JNHMaxB6M6BWFiduO7EFmMGFxl3tvovKNmsYyuTwAEDOuJG', 'User', 100.00, 'manila', 'uploads/user.jpeg', NULL, NULL, 'System', 'New user registered'),
(21, '2024-07-16 01:57:19', 'C', 118, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'justine3', 'justine3@gmail.com', '09176860046', '$2y$10$id/KB2vEO6Eh3BcogCF1Rea8RyNdveOXK5RGlpRJsvkzdfTRRcn5a', 'User', 100.00, 'manila', 'uploads/user.jpeg', NULL, NULL, 'System', 'New user registered'),
(22, '2024-07-16 01:57:50', 'U', 118, 'justine3', 'justine3@gmail.com', '09176860046', '$2y$10$id/KB2vEO6Eh3BcogCF1Rea8RyNdveOXK5RGlpRJsvkzdfTRRcn5a', 'User', 100.00, 'manila', 'uploads/user.jpeg', NULL, NULL, 'justine3', 'justine3@gmail.com', '09176860046', '$2y$10$id/KB2vEO6Eh3BcogCF1Rea8RyNdveOXK5RGlpRJsvkzdfTRRcn5a', 'User', 100.00, 'manila', 'uploads/taylor.jpeg', NULL, NULL, 'System', 'Updated user details'),
(23, '2024-07-16 02:01:04', 'U', 116, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 1, NULL, 'System', 'Updated user details'),
(24, '2024-07-16 02:01:24', 'U', 116, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 1, NULL, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 2, NULL, 'System', 'Updated user details'),
(25, '2024-07-16 02:01:24', 'U', 116, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 2, NULL, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(26, '2024-07-16 02:01:50', 'U', 118, 'justine3', 'justine3@gmail.com', '09176860046', '$2y$10$id/KB2vEO6Eh3BcogCF1Rea8RyNdveOXK5RGlpRJsvkzdfTRRcn5a', 'User', 100.00, 'manila', 'uploads/taylor.jpeg', NULL, NULL, 'justine3', 'justine3@gmail.com', '09176860046', '$2y$10$id/KB2vEO6Eh3BcogCF1Rea8RyNdveOXK5RGlpRJsvkzdfTRRcn5a', 'User', 100.00, 'manila', 'uploads/taylor.jpeg', 1, NULL, 'System', 'Updated user details'),
(27, '2024-07-16 02:01:58', 'U', 118, 'justine3', 'justine3@gmail.com', '09176860046', '$2y$10$id/KB2vEO6Eh3BcogCF1Rea8RyNdveOXK5RGlpRJsvkzdfTRRcn5a', 'User', 100.00, 'manila', 'uploads/taylor.jpeg', 1, NULL, 'justine3', 'justine3@gmail.com', '09176860046', '$2y$10$id/KB2vEO6Eh3BcogCF1Rea8RyNdveOXK5RGlpRJsvkzdfTRRcn5a', 'User', 100.00, 'manila', 'uploads/taylor.jpeg', 2, NULL, 'System', 'Updated user details'),
(28, '2024-07-16 02:01:58', 'U', 118, 'justine3', 'justine3@gmail.com', '09176860046', '$2y$10$id/KB2vEO6Eh3BcogCF1Rea8RyNdveOXK5RGlpRJsvkzdfTRRcn5a', 'User', 100.00, 'manila', 'uploads/taylor.jpeg', 2, NULL, 'justine3', 'justine3@gmail.com', '09176860046', '$2y$10$id/KB2vEO6Eh3BcogCF1Rea8RyNdveOXK5RGlpRJsvkzdfTRRcn5a', 'User', 100.00, 'manila', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(29, '2024-07-16 02:06:06', 'C', 119, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'justine4', 'justine4@gmail.com', '09176860046', '$2y$10$p2sCHfPoIrcoKK/6QlwTuekaQvV4L8fEcNZ5EFGx3S/HaRJGYqzNW', 'User', 100.00, 'manila', 'uploads/user.jpeg', NULL, NULL, 'System', 'New user registered'),
(30, '2024-07-16 02:06:26', 'U', 119, 'justine4', 'justine4@gmail.com', '09176860046', '$2y$10$p2sCHfPoIrcoKK/6QlwTuekaQvV4L8fEcNZ5EFGx3S/HaRJGYqzNW', 'User', 100.00, 'manila', 'uploads/user.jpeg', NULL, NULL, 'justine4', 'justine4@gmail.com', '09176860046', '$2y$10$p2sCHfPoIrcoKK/6QlwTuekaQvV4L8fEcNZ5EFGx3S/HaRJGYqzNW', 'User', 100.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(31, '2024-07-16 02:06:26', 'U', 119, 'justine4', 'justine4@gmail.com', '09176860046', '$2y$10$p2sCHfPoIrcoKK/6QlwTuekaQvV4L8fEcNZ5EFGx3S/HaRJGYqzNW', 'User', 100.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'justine4', 'justine4@gmail.com', '09176860046', '$2y$10$p2sCHfPoIrcoKK/6QlwTuekaQvV4L8fEcNZ5EFGx3S/HaRJGYqzNW', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(32, '2024-07-16 02:09:55', 'U', 116, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 1, NULL, 'System', 'Updated user details'),
(33, '2024-07-16 02:09:55', 'U', 116, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 1, NULL, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(34, '2024-07-16 02:10:25', 'U', 116, 'justine1!!!!hihi', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'justine1', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(35, '2024-07-16 02:13:33', 'U', 116, 'justine1', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'justine1!!', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(36, '2024-07-16 02:15:05', 'U', 116, 'justine1!!', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'justine1!!', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(37, '2024-07-16 02:15:05', 'U', 116, 'justine1!!', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 1, NULL, 'justine1!!', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(38, '2024-07-16 02:15:18', 'U', 116, 'justine1!!', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'justine1', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(39, '2024-07-16 02:15:26', 'U', 116, 'justine1', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'justine1!!!', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(40, '2024-07-16 02:15:31', 'U', 116, 'justine1!!!', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'justine', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(41, '2024-07-16 02:15:38', 'U', 116, 'justine', 'justine1!@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'justine', 'justine1@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(42, '2024-07-16 02:15:52', 'U', 116, 'justine', 'justine1@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'justine', 'justine1@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(43, '2024-07-16 02:19:06', 'U', 116, 'justine', 'justine1@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'justine', 'justine1@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 1, NULL, 'System', 'Updated user details'),
(44, '2024-07-16 02:19:06', 'U', 116, 'justine', 'justine1@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 1, NULL, 'justine', 'justine1@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(45, '2024-07-16 02:21:05', 'U', 116, 'justine', 'justine1@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'justine', 'justine1@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 1, NULL, 'System', 'Updated user details'),
(46, '2024-07-16 02:21:05', 'U', 116, 'justine', 'justine1@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 1, NULL, 'justine', 'justine1@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(47, '2024-07-16 02:21:17', 'U', 116, 'justine', 'justine1@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(48, '2024-07-16 02:21:30', 'U', 116, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$Z71RqXbIRQ4oiSz5w/MnV./zvWK0186RJNDcSoBXrJyjotGnih2h6', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$mtdovhNvdRliGGiBmCXe2uiIL29A70PPr5mm9GSvd6ltgFZ2mIRAe', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(49, '2024-07-16 02:21:42', 'U', 116, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$mtdovhNvdRliGGiBmCXe2uiIL29A70PPr5mm9GSvd6ltgFZ2mIRAe', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/taylor.jpeg', 0, NULL, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$mtdovhNvdRliGGiBmCXe2uiIL29A70PPr5mm9GSvd6ltgFZ2mIRAe', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(50, '2024-07-16 02:22:03', 'U', 116, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$mtdovhNvdRliGGiBmCXe2uiIL29A70PPr5mm9GSvd6ltgFZ2mIRAe', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$mtdovhNvdRliGGiBmCXe2uiIL29A70PPr5mm9GSvd6ltgFZ2mIRAe', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(51, '2024-07-16 02:22:20', 'U', 116, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$mtdovhNvdRliGGiBmCXe2uiIL29A70PPr5mm9GSvd6ltgFZ2mIRAe', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 1, NULL, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$mtdovhNvdRliGGiBmCXe2uiIL29A70PPr5mm9GSvd6ltgFZ2mIRAe', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 2, NULL, 'System', 'Updated user details'),
(52, '2024-07-16 02:22:20', 'U', 116, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$mtdovhNvdRliGGiBmCXe2uiIL29A70PPr5mm9GSvd6ltgFZ2mIRAe', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 2, NULL, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$mtdovhNvdRliGGiBmCXe2uiIL29A70PPr5mm9GSvd6ltgFZ2mIRAe', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(53, '2024-07-16 02:23:01', 'U', 116, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$mtdovhNvdRliGGiBmCXe2uiIL29A70PPr5mm9GSvd6ltgFZ2mIRAe', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$ZqWYiEiKpWT3NLooLy/UzuTLxubfvbkJbEFZfhXuhJ0c/yU8vCKWy', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(54, '2024-07-16 02:23:09', 'U', 116, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$ZqWYiEiKpWT3NLooLy/UzuTLxubfvbkJbEFZfhXuhJ0c/yU8vCKWy', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/user.jpeg', 0, NULL, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$ZqWYiEiKpWT3NLooLy/UzuTLxubfvbkJbEFZfhXuhJ0c/yU8vCKWy', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/293066135_768748097491834_8197535814127481849_n.jpg', 0, NULL, 'System', 'Updated user details'),
(55, '2024-07-16 02:25:12', 'U', 116, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$ZqWYiEiKpWT3NLooLy/UzuTLxubfvbkJbEFZfhXuhJ0c/yU8vCKWy', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/293066135_768748097491834_8197535814127481849_n.jpg', 0, NULL, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$ZqWYiEiKpWT3NLooLy/UzuTLxubfvbkJbEFZfhXuhJ0c/yU8vCKWy', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/293066135_768748097491834_8197535814127481849_n.jpg', 1, NULL, 'System', 'Updated user details'),
(56, '2024-07-16 02:25:13', 'U', 116, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$ZqWYiEiKpWT3NLooLy/UzuTLxubfvbkJbEFZfhXuhJ0c/yU8vCKWy', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/293066135_768748097491834_8197535814127481849_n.jpg', 1, NULL, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$ZqWYiEiKpWT3NLooLy/UzuTLxubfvbkJbEFZfhXuhJ0c/yU8vCKWy', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/293066135_768748097491834_8197535814127481849_n.jpg', 0, NULL, 'System', 'Updated user details'),
(57, '2024-07-16 02:31:46', 'U', 116, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$ZqWYiEiKpWT3NLooLy/UzuTLxubfvbkJbEFZfhXuhJ0c/yU8vCKWy', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/293066135_768748097491834_8197535814127481849_n.jpg', 0, NULL, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$ZqWYiEiKpWT3NLooLy/UzuTLxubfvbkJbEFZfhXuhJ0c/yU8vCKWy', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/293066135_768748097491834_8197535814127481849_n.jpg', 1, NULL, 'System', 'Updated user details'),
(58, '2024-07-16 02:31:46', 'U', 116, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$ZqWYiEiKpWT3NLooLy/UzuTLxubfvbkJbEFZfhXuhJ0c/yU8vCKWy', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/293066135_768748097491834_8197535814127481849_n.jpg', 1, NULL, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$ZqWYiEiKpWT3NLooLy/UzuTLxubfvbkJbEFZfhXuhJ0c/yU8vCKWy', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/293066135_768748097491834_8197535814127481849_n.jpg', 0, NULL, 'System', 'Updated user details'),
(59, '2024-07-16 09:54:02', 'U', 116, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$ZqWYiEiKpWT3NLooLy/UzuTLxubfvbkJbEFZfhXuhJ0c/yU8vCKWy', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/293066135_768748097491834_8197535814127481849_n.jpg', 0, NULL, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$ZqWYiEiKpWT3NLooLy/UzuTLxubfvbkJbEFZfhXuhJ0c/yU8vCKWy', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/293066135_768748097491834_8197535814127481849_n.jpg', 1, NULL, 'System', 'Updated user details'),
(60, '2024-07-16 09:54:02', 'U', 116, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$ZqWYiEiKpWT3NLooLy/UzuTLxubfvbkJbEFZfhXuhJ0c/yU8vCKWy', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/293066135_768748097491834_8197535814127481849_n.jpg', 1, NULL, 'justine anne', 'justine1@gmail.com', '09176860040', '$2y$10$ZqWYiEiKpWT3NLooLy/UzuTLxubfvbkJbEFZfhXuhJ0c/yU8vCKWy', 'User', 100.00, '1724 Taft Avenue Pasay Cityyyy Ey ', 'uploads/293066135_768748097491834_8197535814127481849_n.jpg', 0, NULL, 'System', 'Updated user details'),
(61, '2024-07-16 10:02:37', 'C', 120, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'anne', 'anne@gmail.com', '09176860046', '$2y$10$sb.eS9A0ZBwJWsnh1A7rSe91XiUjimUNd6jtUhudY0nvmEy8Gd4va', 'User', 100.00, 'manila', 'uploads/293066135_768748097491834_8197535814127481849_n.jpg', NULL, NULL, 'System', 'New user registered'),
(62, '2024-07-16 10:06:48', 'C', 121, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'test1', 'test1@gmail.com', '09176860046', '$2y$10$aO4xliEm5.gbf9/s/CmbF.GqNEcU9wJE3nWg0XxGDAdoDxGhk6bTS', 'User', 100.00, 'manila', 'uploads/user.jpeg', NULL, NULL, 'System', 'New user registered'),
(63, '2024-07-16 10:10:54', 'U', 121, 'test1', 'test1@gmail.com', '09176860046', '$2y$10$aO4xliEm5.gbf9/s/CmbF.GqNEcU9wJE3nWg0XxGDAdoDxGhk6bTS', 'User', 100.00, 'manila', 'uploads/user.jpeg', NULL, NULL, 'test1', 'test1@gmail.com', '09176860046', '$2y$10$aO4xliEm5.gbf9/s/CmbF.GqNEcU9wJE3nWg0XxGDAdoDxGhk6bTS', 'User', 100.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(64, '2024-07-16 10:10:54', 'U', 121, 'test1', 'test1@gmail.com', '09176860046', '$2y$10$aO4xliEm5.gbf9/s/CmbF.GqNEcU9wJE3nWg0XxGDAdoDxGhk6bTS', 'User', 100.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'test1', 'test1@gmail.com', '09176860046', '$2y$10$aO4xliEm5.gbf9/s/CmbF.GqNEcU9wJE3nWg0XxGDAdoDxGhk6bTS', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(65, '2024-07-16 10:12:07', 'U', 121, 'test1', 'test1@gmail.com', '09176860046', '$2y$10$aO4xliEm5.gbf9/s/CmbF.GqNEcU9wJE3nWg0XxGDAdoDxGhk6bTS', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1', 'test1@gmail.com', '09176860046', '$2y$10$aO4xliEm5.gbf9/s/CmbF.GqNEcU9wJE3nWg0XxGDAdoDxGhk6bTS', 'User', 100.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(66, '2024-07-16 10:12:07', 'U', 121, 'test1', 'test1@gmail.com', '09176860046', '$2y$10$aO4xliEm5.gbf9/s/CmbF.GqNEcU9wJE3nWg0XxGDAdoDxGhk6bTS', 'User', 100.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'test1', 'test1@gmail.com', '09176860046', '$2y$10$aO4xliEm5.gbf9/s/CmbF.GqNEcU9wJE3nWg0XxGDAdoDxGhk6bTS', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(67, '2024-07-16 10:12:21', 'U', 121, 'test1', 'test1@gmail.com', '09176860046', '$2y$10$aO4xliEm5.gbf9/s/CmbF.GqNEcU9wJE3nWg0XxGDAdoDxGhk6bTS', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1', 'test1@gmail.com', '09176860046', '$2y$10$aO4xliEm5.gbf9/s/CmbF.GqNEcU9wJE3nWg0XxGDAdoDxGhk6bTS', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(68, '2024-07-16 10:12:37', 'U', 121, 'test1', 'test1@gmail.com', '09176860046', '$2y$10$aO4xliEm5.gbf9/s/CmbF.GqNEcU9wJE3nWg0XxGDAdoDxGhk6bTS', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1', 'test1@gmail.com', '09176860046', '$2y$10$aO4xliEm5.gbf9/s/CmbF.GqNEcU9wJE3nWg0XxGDAdoDxGhk6bTS', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(69, '2024-07-16 10:13:02', 'U', 121, 'test1', 'test1@gmail.com', '09176860046', '$2y$10$aO4xliEm5.gbf9/s/CmbF.GqNEcU9wJE3nWg0XxGDAdoDxGhk6bTS', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$aO4xliEm5.gbf9/s/CmbF.GqNEcU9wJE3nWg0XxGDAdoDxGhk6bTS', 'User', 100.00, 'manila', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(70, '2024-07-16 10:13:19', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$aO4xliEm5.gbf9/s/CmbF.GqNEcU9wJE3nWg0XxGDAdoDxGhk6bTS', 'User', 100.00, 'manila', 'uploads/taylor.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$jG0EVmLuRkV2YnRGSIAdXuXC20Kw.JVmVbDJG3ybD0FBzcWItLIbe', 'User', 100.00, 'manila', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(71, '2024-07-16 10:13:31', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$jG0EVmLuRkV2YnRGSIAdXuXC20Kw.JVmVbDJG3ybD0FBzcWItLIbe', 'User', 100.00, 'manila', 'uploads/taylor.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/taylor.jpeg', 0, NULL, 'System', 'Updated user details'),
(72, '2024-07-16 10:16:58', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/taylor.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(73, '2024-07-16 11:44:11', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(74, '2024-07-16 11:44:11', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(75, '2024-07-16 11:46:12', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 1100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(76, '2024-07-16 11:46:16', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 1100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 700.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(77, '2024-07-16 11:47:11', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 700.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 300.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(78, '2024-07-16 11:50:59', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 300.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 300.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(79, '2024-07-16 11:50:59', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 300.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 300.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(80, '2024-07-16 11:52:28', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 300.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(81, '2024-07-16 11:54:09', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 1100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(82, '2024-07-16 11:54:17', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 1100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 900.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(83, '2024-07-16 11:58:20', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 900.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 700.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(84, '2024-07-16 12:01:49', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 700.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 500.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(85, '2024-07-16 12:10:48', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 500.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 500.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(86, '2024-07-16 12:10:48', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 500.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 500.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(87, '2024-07-16 12:11:19', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 500.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 320.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(88, '2024-07-16 12:15:26', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 320.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(89, '2024-07-16 12:15:37', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(90, '2024-07-16 12:19:32', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 180.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(91, '2024-07-16 12:19:33', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 180.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 200.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(92, '2024-07-16 12:19:33', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 200.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 220.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(93, '2024-07-16 12:19:33', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 220.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 240.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(94, '2024-07-16 12:19:33', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 240.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 260.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(95, '2024-07-16 12:19:33', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 260.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 280.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(96, '2024-07-16 12:19:34', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 280.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 300.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(97, '2024-07-16 12:19:34', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 300.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 320.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(98, '2024-07-16 12:19:34', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 320.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(99, '2024-07-16 12:19:34', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 360.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(100, '2024-07-16 12:19:34', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 360.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 380.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(101, '2024-07-16 12:19:34', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 380.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 400.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(102, '2024-07-16 12:19:34', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 400.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 420.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(103, '2024-07-16 12:19:39', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 420.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 240.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(104, '2024-07-16 12:28:14', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 240.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 240.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(105, '2024-07-16 12:28:19', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 240.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 240.00, 'manila', 'uploads/user.jpeg', 2, NULL, 'System', 'Updated user details'),
(106, '2024-07-16 12:28:19', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 240.00, 'manila', 'uploads/user.jpeg', 2, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 240.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(107, '2024-07-16 12:28:46', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 240.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 60.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(108, '2024-07-16 12:28:56', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 60.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 1060.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(109, '2024-07-16 12:29:04', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 1060.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 700.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(110, '2024-07-16 12:56:46', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 700.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(111, '2024-07-16 12:57:14', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(112, '2024-07-16 12:57:30', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 2, NULL, 'System', 'Updated user details'),
(113, '2024-07-16 12:57:30', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 2, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details');
INSERT INTO `users_audit` (`audit_id`, `audit_timestamp`, `activity`, `user_id`, `old_fullname`, `old_email`, `old_phoneNumber`, `old_password`, `old_role`, `old_wallet`, `old_address`, `old_picture`, `old_login_attempts`, `old_ban_time`, `new_fullname`, `new_email`, `new_phoneNumber`, `new_password`, `new_role`, `new_wallet`, `new_address`, `new_picture`, `new_login_attempts`, `new_ban_time`, `end_user`, `end_reason`) VALUES
(114, '2024-07-16 13:00:06', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(115, '2024-07-16 13:00:06', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(116, '2024-07-16 13:00:23', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(117, '2024-07-16 13:45:37', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(118, '2024-07-16 13:45:43', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 2, NULL, 'System', 'Updated user details'),
(119, '2024-07-16 13:45:43', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 2, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(120, '2024-07-16 13:54:37', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(121, '2024-07-16 13:54:37', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(122, '2024-07-16 14:03:07', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'System', 'Updated user details'),
(123, '2024-07-16 14:03:07', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'System', 'Updated user details'),
(124, '2024-07-16 14:04:16', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'System', 'Updated user details'),
(125, '2024-07-16 14:04:16', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'System', 'Updated user details'),
(126, '2024-07-16 14:06:58', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'System', 'Updated user details'),
(127, '2024-07-16 14:06:58', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'System', 'Updated user details'),
(128, '2024-07-16 14:07:28', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(129, '2024-07-16 14:07:32', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 2, NULL, 'System', 'Updated user details'),
(130, '2024-07-16 14:07:33', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 2, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(131, '2024-07-16 14:08:58', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(132, '2024-07-16 14:08:58', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(133, '2024-07-16 14:10:04', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'System', 'Updated user details'),
(134, '2024-07-16 14:10:04', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'System', 'Updated user details'),
(135, '2024-07-16 14:14:34', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'System', 'Updated user details'),
(136, '2024-07-16 14:14:34', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'System', 'Updated user details'),
(137, '2024-07-16 14:19:27', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(138, '2024-07-16 14:19:27', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(139, '2024-07-16 14:19:53', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 180.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(140, '2024-07-16 14:19:53', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 180.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 200.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(141, '2024-07-16 14:19:53', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 200.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 220.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(142, '2024-07-16 14:19:53', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 220.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 240.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(143, '2024-07-16 14:19:53', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 240.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 260.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(144, '2024-07-16 14:19:54', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 260.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 280.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(145, '2024-07-16 14:19:54', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 280.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 300.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(146, '2024-07-16 14:19:54', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 300.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 320.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(147, '2024-07-16 14:19:54', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 320.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(148, '2024-07-16 14:20:02', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 340.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(149, '2024-07-16 14:27:55', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'System', 'Updated user details'),
(150, '2024-07-16 14:27:55', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'System', 'Updated user details'),
(151, '2024-07-16 14:28:46', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'System', 'Updated user details'),
(152, '2024-07-16 14:28:46', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'System', 'Updated user details'),
(153, '2024-07-16 14:38:23', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'System', 'Updated user details'),
(154, '2024-07-16 14:38:23', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'System', 'Updated user details'),
(155, '2024-07-16 14:53:26', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'System', 'Updated user details'),
(156, '2024-07-16 14:53:26', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'System', 'Updated user details'),
(157, '2024-07-16 14:55:54', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'System', 'Updated user details'),
(158, '2024-07-16 14:55:54', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'System', 'Updated user details'),
(159, '2024-07-16 15:09:46', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(160, '2024-07-16 15:09:46', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(161, '2024-07-16 19:28:05', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 160.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 180.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(162, '2024-07-16 19:28:05', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 180.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 200.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(163, '2024-07-16 19:28:06', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 200.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 220.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(164, '2024-07-16 19:28:06', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 220.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 240.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(165, '2024-07-16 19:28:06', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 240.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 260.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(166, '2024-07-16 19:28:07', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 260.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 280.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(167, '2024-07-16 19:28:07', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 280.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 300.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(168, '2024-07-16 19:28:07', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 300.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 320.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(169, '2024-07-16 19:28:12', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 320.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(170, '2024-07-16 21:38:27', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'System', 'Updated user details'),
(171, '2024-07-16 21:38:27', 'U', 115, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 1, NULL, 'admin', 'admin@example.com', '09176861123', '$2y$10$gjQFQEUu4iJsmzFnZxFvYuxvPVebXKmIaS9f2LYs8noSFAK1aRf6e', 'Administrator', 100.00, '1724 Taft Avenue Pasay City', '', 0, NULL, 'System', 'Updated user details'),
(172, '2024-07-16 21:41:25', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(173, '2024-07-16 21:41:25', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(174, '2024-07-16 21:42:41', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'System', 'Updated user details'),
(175, '2024-07-16 21:42:41', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 1, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(176, '2024-07-16 21:44:11', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 1100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(177, '2024-07-16 21:44:46', 'U', 121, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 1100.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'test1hihi', 'test1@gmail.com', '09176860047', '$2y$10$NKdI6w8Jt240m61RaG3g..os4zp5LdRClOJ.ekizzxAM50QI09SZG', 'User', 880.00, 'manila', 'uploads/user.jpeg', 0, NULL, 'System', 'Updated user details'),
(178, '2024-07-16 23:50:41', 'C', 122, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Banker Joe', 'banker@mail.com', '09176566235', '$2y$10$OPLckX7Q2OL6VlG/zcocFOiMK5CnuQUYENbhQQgKTijY0dP8Cu4k6', 'User', 10000.00, '1411 taft', 'uploads/415919457_24688718220741925_2080994886690690471_n.jpg', NULL, NULL, 'System', 'New user registered'),
(179, '2024-07-16 23:51:04', 'U', 122, 'Banker Joe', 'banker@mail.com', '09176566235', '$2y$10$OPLckX7Q2OL6VlG/zcocFOiMK5CnuQUYENbhQQgKTijY0dP8Cu4k6', 'User', 10000.00, '1411 taft', 'uploads/415919457_24688718220741925_2080994886690690471_n.jpg', NULL, NULL, 'Banker Joe', 'banker@mail.com', '09176566235', '$2y$10$OPLckX7Q2OL6VlG/zcocFOiMK5CnuQUYENbhQQgKTijY0dP8Cu4k6', 'User', 10000.00, '1411 taft', 'uploads/415919457_24688718220741925_2080994886690690471_n.jpg', 1, NULL, 'System', 'Updated user details'),
(180, '2024-07-16 23:51:04', 'U', 122, 'Banker Joe', 'banker@mail.com', '09176566235', '$2y$10$OPLckX7Q2OL6VlG/zcocFOiMK5CnuQUYENbhQQgKTijY0dP8Cu4k6', 'User', 10000.00, '1411 taft', 'uploads/415919457_24688718220741925_2080994886690690471_n.jpg', 1, NULL, 'Banker Joe', 'banker@mail.com', '09176566235', '$2y$10$OPLckX7Q2OL6VlG/zcocFOiMK5CnuQUYENbhQQgKTijY0dP8Cu4k6', 'User', 10000.00, '1411 taft', 'uploads/415919457_24688718220741925_2080994886690690471_n.jpg', 0, NULL, 'System', 'Updated user details'),
(181, '2024-07-16 23:51:20', 'U', 122, 'Banker Joe', 'banker@mail.com', '09176566235', '$2y$10$OPLckX7Q2OL6VlG/zcocFOiMK5CnuQUYENbhQQgKTijY0dP8Cu4k6', 'User', 10000.00, '1411 taft', 'uploads/415919457_24688718220741925_2080994886690690471_n.jpg', 0, NULL, 'Banker Joe', 'banker@mail.com', '09176566235', '$2y$10$OPLckX7Q2OL6VlG/zcocFOiMK5CnuQUYENbhQQgKTijY0dP8Cu4k6', 'User', 9820.00, '1411 taft', 'uploads/415919457_24688718220741925_2080994886690690471_n.jpg', 0, NULL, 'System', 'Updated user details'),
(184, '2024-07-16 23:55:20', 'U', 122, 'Banker Joe', 'banker@mail.com', '09176566235', '$2y$10$OPLckX7Q2OL6VlG/zcocFOiMK5CnuQUYENbhQQgKTijY0dP8Cu4k6', 'User', 9820.00, '1411 taft', 'uploads/415919457_24688718220741925_2080994886690690471_n.jpg', 0, NULL, 'Banker Joe', 'banker@mail.com', '09176566235', '$2y$10$OPLckX7Q2OL6VlG/zcocFOiMK5CnuQUYENbhQQgKTijY0dP8Cu4k6', 'User', 9640.00, '1411 taft', 'uploads/415919457_24688718220741925_2080994886690690471_n.jpg', 0, NULL, 'System', 'Updated user details');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `combo_meals`
--
ALTER TABLE `combo_meals`
  ADD PRIMARY KEY (`combo_id`);

--
-- Indexes for table `combo_meals_audit`
--
ALTER TABLE `combo_meals_audit`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`menu_item_id`);

--
-- Indexes for table `menu_items_audit`
--
ALTER TABLE `menu_items_audit`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `orders_audit`
--
ALTER TABLE `orders_audit`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indexes for table `specials_audit`
--
ALTER TABLE `specials_audit`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indexes for table `users_audit`
--
ALTER TABLE `users_audit`
  ADD PRIMARY KEY (`audit_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `combo_meals_audit`
--
ALTER TABLE `combo_meals_audit`
  MODIFY `audit_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items_audit`
--
ALTER TABLE `menu_items_audit`
  MODIFY `audit_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders_audit`
--
ALTER TABLE `orders_audit`
  MODIFY `audit_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `specials_audit`
--
ALTER TABLE `specials_audit`
  MODIFY `audit_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users_audit`
--
ALTER TABLE `users_audit`
  MODIFY `audit_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
