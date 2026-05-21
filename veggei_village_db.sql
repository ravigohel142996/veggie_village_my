-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2026 at 07:59 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vaggie_village`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `password`) VALUES
(1, 'Admin', 'admin@gmail.com', '12345');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `short_desc` varchar(250) NOT NULL,
  `long_desc` varchar(500) NOT NULL,
  `image` varchar(200) DEFAULT NULL,
  `data_status` varchar(50) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `short_desc`, `long_desc`, `image`, `data_status`) VALUES
(7, 'North Indian', 'This is a popular category in Northern India', 'Indian cuisine encompasses a wide variety of regional cuisine native to India. Given the range of diversity in soil type, climate and occupations, these cuisines vary significantly from each other and use locally available chocolates, herbs, vegetables and fruits. The dishes are then served according to taste in either mild, medium or hot. Indian food is also heavily influenced by religious and cultural choices, like Hinduism and traditions.', 'north indian food.jpeg', 'Active'),
(8, 'Chinese', 'Indo-Chinese fusion dishes with bold sauces and stir-fried flavors.', 'This category features Indo-Chinese delicacies that combine the essence of Chinese stir-fry techniques with Indian spices. Enjoy popular favorites like Hakka noodles, Manchurian, fried rice, and spicy starters that deliver a perfect balance of flavor and texture.', 'chines food.jpeg', 'Active'),
(9, 'South Indian', ' Light, healthy meals with rice, lentils, and coconut flavors.', 'South Indian cuisine is celebrated for its light and nutritious offerings such as dosa, idli, and sambhar. These dishes are made using rice, lentils, and spices, often paired with flavorful chutneys and are ideal for breakfast or a light meal.', 'south indian food.jpeg', 'Active'),
(10, 'Snacks', ' A snack is a small portion of food eaten between meals.', 'A snack is a small portion of food eaten between meals. This may be a snack food, such as potato chips or baby carrots, but can also simply be a small amount of any food.', 'snacks food.jpeg', 'Active'),
(11, 'Himalayan Food', 'Nepalese cuisine comprises a variety of cuisines based upon ethnicity, soil and climate relating to Nepal cultural diversity and geography.', 'Much of the cuisine is variation on Asian themes. Other foods have hybrid Tibetan, Indian and Thai origins. They were originally filled with buffalo meat but now also with goat or chicken, as well as vegetarian preparations. Special foods such as sel roti, finni roti and patre are eaten during festivals such as Tihar.', 'himaliyan food.jpg', 'Active'),
(33, 'Indian', 'Traditional Indian flavors with rich spices and diverse vegetarian and non-vegetarian dishes.', 'Indian cuisine is known for its bold spices, aromatic gravies, and regional diversity. From creamy butter chicken to hearty vegetarian dishes like dal makhani and paneer butter masala, this category offers comforting, flavorful meals rooted in centuries of culinary tradition.', 'indian_categorie.jpg', 'Active'),
(34, 'Pizza', 'Hand-tossed pizzas with a variety of toppings and melted cheese.', 'A delicious range of pizzas featuring soft crusts, tangy sauces, and generous cheese. Choose from classic Margherita to spicy Paneer Tikka and BBQ Chicken, satisfying both vegetarian and meat lovers with fresh ingredients and rich flavor.', 'pizza_categorie.webp', 'Active'),
(35, 'Drinks ', 'Refreshing cold beverages, shakes, and traditional coolers.', 'Quench your thirst with our curated beverage list, including fizzy sodas, fresh juices, traditional Indian coolers like lassi and chaas, and rich milkshakes. Perfect to accompany your meal or enjoy on a hot day.', 'drinks_categories.jpg', 'Active'),
(36, 'Burger', 'Juicy burgers stacked with flavorful patties, sauces, and veggies.', 'This category features delicious burgers layered with fresh lettuce, sauces, cheese, and flavorful patties. Whether vegetarian or chicken, each burger offers a satisfying mix of taste and texture.', 'burger_categories.avif', 'Active'),
(37, 'Snacks and Starters', 'Quick bites and crispy starters to satisfy hunger in minutes.', 'Perfect for munching or starting off a meal, this category includes savory snacks like samosas, pakoras, fries, and Indo-Chinese starters like spring rolls and chicken 65. These appetizers are crunchy, flavorful, and irresistible.', 'snacks$starter_categories.jpg', 'Active'),
(38, 'Punjabi', 'Rich and hearty dishes from the land of Punjab.', ' Punjabi cuisine is known for its robust flavors and buttery textures. This category features popular North Indian dishes like butter chicken, sarson da saag, and rajma chawal, often served with naan or makki di roti and a tall glass of lassi.', 'punjabi_categories.jpg', 'Active'),
(39, 'Italian', 'Authentic Italian pastas, pizzas, and cheesy delights.', 'Experience the taste of Italy with creamy pastas, aromatic pizzas, and rich desserts. Whether a slice of Margherita pizza, or a spoonful of tiramisu, Italian food is all about comfort and classic flavors.', 'italian_categories.jpg', 'Active'),
(40, 'Desserts', 'Sweet treats to end your meal on a delightful note.', 'Satisfy your sweet tooth with a variety of classic and contemporary desserts. From rich Indian sweets like gulab jamun and rasmalai to indulgent chocolate brownies, ice cream, and international favorites like tiramisu, there is something for every dessert lover.', 'deserts_categories.jpg', 'Active'),
(41, 'Healthy and Diet', 'Nutritious meals made with low-oil and fresh ingredients.', 'For health-conscious foodies, this category offers balanced meals rich in fiber, protein, and essential nutrients. Dishes like quinoa salad, oats chilla, grilled wraps, and smoothie bowls make it easy to eat well without compromising on taste.', 'healthy_diet_categories.jpg', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `food`
--

CREATE TABLE IF NOT EXISTS `food` (
  `id` int(11) NOT NULL,
  `cat_id` int(10) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `image` varchar(200) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `data_status` varchar(50) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `food`
--

INSERT INTO `food` (`id`, `cat_id`, `fname`, `description`, `image`, `price`, `data_status`) VALUES
(1, 9, 'Dosa', 'I love Dosa very much. Its a South Indian Food and Everybody loves it!', 'dosa.jpeg', 99, 'Active'),
(8, 8, 'Chowmin', 'This is a Chinese Pop Food. Everybody likes it so damn very much.', 'chowmin.jpeg', 80, 'Active'),
(9, 10, 'French Fries', 'This is a Snacks Food. Everybody likes it so damn very much with Tea or Coffee.', 'french fries.jpeg', 80, 'Active'),
(10, 11, 'Momos', 'This is a Himalayan Pop Food. Everybody likes it so damn very much. Its comes with different flavors!', 'momos.jpeg', 70, 'Active'),
(11, 8, 'Hakka Noodles', 'This food is so much popular even in India. It tastes like Chowmein but with Gravy. ', 'hakkka nodels.jpeg', 80, 'Active'),
(27, 33, 'Paneer Butter Masala', 'Soft paneer cubes cooked in creamy tomato gravy.', 'Paneer Butter Masala.jpg', 180, 'Active'),
(28, 33, 'Chole Bhature', 'Spicy chickpeas served with deep-fried bread.', 'Chole Bhature.jpeg', 100, 'Active'),
(29, 8, 'Schezwan Fried Rice', 'Basmati rice stir fried with chopped vegetables and spicy red chili garlic sauce', 'Schezwan Fried Rice.jpg', 140, 'Active'),
(30, 7, 'Spring Rolls', 'Fried thin wrappers filled with cabbage carrots and sauces served as a starter', 'spring rolls.jpg', 90, 'Active'),
(31, 9, 'Idli Sambhar', 'Soft rice idlis served with hot lentil sambhar and coconut chutney', 'Idli Sambhar.webp', 60, 'Active'),
(32, 9, 'Medu Vada', 'Urad dal batter shaped into rings and deep fried served with sambhar', 'medu vada.jpg', 70, 'Active'),
(33, 34, 'Margherita', 'Thin crust pizza topped with mozzarella cheese and tomato sauce', 'Margherita.jpg', 140, 'Active'),
(34, 34, 'Veggie Supreme', 'Combination of sweet corn onion capsicum tomato on a soft cheesy base', 'Veggie Supreme.png', 200, 'Active'),
(35, 34, 'Mushroom Pizza', 'Baked pizza layered with mushroom slices oregano and mozzarella', 'Mushroom Pizza.jpg', 170, 'Active'),
(36, 36, 'Veg Burger', 'Vegetable patty layered with tomato lettuce and mayo inside soft bun', 'Veg Burger.webp', 90, 'Active'),
(37, 36, 'Cheese Burger', 'Crispy veggie patty topped with cheese slice and sauces for a creamy taste', 'Cheese Burger.webp', 100, 'Active'),
(38, 35, 'Coke', 'Refreshing chilled cola served with ice', 'Coke.webp', 40, 'Active'),
(39, 35, 'Sprite', 'Sparkling clear drink with lemon lime flavor served cold', 'Sprite.png', 40, 'Active'),
(40, 35, 'Masala Chaas', 'Chilled curd based drink mixed with roasted cumin mint and salt', 'masala-chaas-1.jpg', 40, 'Active'),
(41, 35, 'Sweet Lassi', 'Thick curd blended with sugar and served cold', 'lassi-recipe.jpg', 50, 'Active'),
(42, 35, 'Mango Shake', 'Thick shake made with fresh mango pulp and milk', 'Mango Shake.jpg', 70, 'Active'),
(43, 40, 'Gulab Jamun', 'Fried sweet dumplings soaked in rose flavored sugar syrup', 'Gulab Jamun.jpg', 60, 'Active'),
(44, 40, 'Rasmalai', 'Sweet cottage cheese discs dipped in thickened saffron milk', 'Rasmalai.jpg', 70, 'Active'),
(45, 33, 'Aloo Gobi', 'Potatoes and cauliflower are sauteed with turmeric and dry spices for a classic dry Indian dish', 'Aloo Gobi.jpg', 120, 'Active'),
(46, 33, 'Bhindi Masala', 'Okra pieces are tossed with onion tomato and Indian masalas to make a healthy vegetarian option', 'Bhindi-Masala-2.jpg', 130, 'Active'),
(47, 33, 'Palak Paneer', 'Smooth spinach puree cooked with soft paneer and Indian spices makes a healthy flavorful dish', 'Palak Paneer.webp', 160, 'Active'),
(48, 33, 'Chilli Paneer', 'Paneer tossed with green capsicum onion and spicy soy based chili sauce', 'Chilli Paneer.jpg', 150, 'Active'),
(49, 8, 'Veg Fried Rice', 'Light fried rice mixed with fresh carrots beans spring onion and minimal spices', 'Mixed-Veg-Rice-Delight_-done.png', 120, 'Active'),
(50, 9, 'Upma', 'Healthy breakfast dish made with roasted semolina tempered with spices and veggies', 'Upma.jpeg', 60, 'Active'),
(51, 9, 'Rava Dosa', 'Instant dosa prepared with rava rice flour and spices served hot with chutney', 'rava dosa.jpg', 90, 'Active'),
(52, 36, 'Double Patty Burger', 'Stacked with double veggie patties lettuce cheese and special sauce', 'Double Patty Burger.webp', 130, 'Active'),
(53, 36, 'Paneer Burger', 'Thick paneer patty grilled and served with lettuce and mayo in toasted bun', 'Paneer Burger.jpeg', 110, 'Active'),
(54, 40, 'Vanilla Ice Cream', 'Classic vanilla ice cream served in cup or cone', 'Vanilla Ice Cream.jpg', 50, 'Active'),
(55, 40, 'Brownie with Ice Cream', 'Warm brownie topped with cold vanilla ice cream and chocolate syrup', 'Brownie with Ice Cream.jpg', 90, 'Active'),
(56, 40, 'Jalebi', 'Crispy hot jalebi coils soaked in sugar syrup served hot', 'Jalebi.jpg', 40, 'Active'),
(57, 10, 'Samosa', 'Deep fried snack with a golden flaky crust filled with seasoned mashed potatoes and peas', 'Samosa.jpg', 30, 'Active'),
(58, 10, 'Pakora', 'Mixed vegetables dipped in a seasoned gram flour batter and fried until golden and crispy', 'Pakora.jpeg', 40, 'Active'),
(59, 10, ' Nachos with Cheese', 'Crisp nacho chips served with hot melted cheese and a touch of spice', 'Nachos with Cheese.jpg', 80, 'Active'),
(60, 10, 'Garlic Bread', 'Slices of bread baked with garlic and herbs for a flavorful bite', 'Garlic Bread.jpeg', 80, 'Active'),
(61, 11, 'Aloo Achar', 'Sliced boiled potatoes tossed in mustard oil with sesame seeds and green chili', 'Aloo Achar.jpg', 80, 'Active'),
(62, 11, 'Tingmo', 'Tibetan-style steamed bun served with hot stew or chutney', 'Tingmo.jpeg', 50, 'Active'),
(63, 11, 'Sel Roti', 'Traditional Nepali homemade fried rice flour doughnut with a slight sweetness', 'Sel Roti.jpg', 40, 'Active'),
(64, 11, 'Gundruk Soup', 'A tangy and nutritious soup made from fermented spinach or mustard leaves', 'Gundruk Soup.jpeg', 60, 'Active'),
(65, 38, 'Rajma Chawal', 'Punjabi-style rajma cooked in onion tomato gravy served with steamed rice', 'Rajma Chawal.jpg', 120, 'Active'),
(66, 38, 'Aloo Paratha', ' Flaky paratha filled with mashed spiced potatoes served with butter and curd', 'Aloo Paratha.jpg', 90, 'Active'),
(67, 38, 'Kadhi Pakora', 'Tangy yogurt based curry simmered with soft besan pakoras and mild spices', 'Kadhi Pakora.jpg', 120, 'Active'),
(68, 41, 'Grilled Paneer Salad', 'Fresh greens tossed with grilled paneer and olive oil lemon dressing', 'Grilled Paneer Salad.jpg', 100, 'Active'),
(69, 41, 'Quinoa Bowl', 'Cooked quinoa mixed with sauted vegetables chickpeas and herbs', 'Quinoa Bowl.jpg', 120, 'Active'),
(70, 41, 'Oats Chilla', ' Healthy chilla made from oats and veggies pan fried with minimal oil', 'Oats Chilla.jpg', 100, 'Active'),
(71, 39, 'Penne Arrabbiata', 'Penne pasta tossed in spicy tangy tomato sauce with herbs and garlic', 'Penne Arrabbiata.jpg', 120, 'Active'),
(72, 39, 'Garlic Bread with Cheese', 'Soft bread slices topped with garlic butter and cheese then baked to perfection', 'Garlic Bread with Cheese.jpg', 90, 'Active'),
(73, 39, 'Bruschetta', 'Italian bread grilled and topped with fresh tomato basil and olive oil', 'Bruschetta.webp', 100, 'Active'),
(74, 7, 'Baingan Bharta', 'Smoked and mashed eggplant cooked with tomatoes onions and spices for an earthy flavor', 'Baingan Bharta.jpeg', 120, 'Active'),
(75, 7, 'Poori Sabzi', 'Puffy wheat bread served with mildly spiced potato and peas curry', 'Poori Sabzi.jpg', 90, 'Active'),
(76, 7, 'Mix Veg Curry', 'Assorted seasonal vegetables cooked in a rich and mildly spiced curry', 'Mix Veg Curry.jpg', 120, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE IF NOT EXISTS `offers` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `discount` varchar(50) DEFAULT NULL,
  `promo_code` varchar(20) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Upcoming',
  `image` varchar(255) NOT NULL,
  `data_status` varchar(50) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `title`, `description`, `discount`, `promo_code`, `start_date`, `end_date`, `status`, `image`, `data_status`) VALUES
(1, 'Weekend Discount', 'Get 20% off on all orders', '20', 'SAVE20', '2025-08-07', '2025-08-22', 'expired', 'banner1.jpg', 'Active'),
(2, 'Lunch Special', 'Flat $10 off on lunch orders', '10', 'LUNCH10', '2026-01-29', '2026-02-06', 'expired', 'banner2.jpg', 'Active'),
(7, 'Summer Sale', 'Get 20% off on all Bluetooth headphones this weekend only!', '12', 'abc22', '2026-01-29', '2026-01-29', 'expired', 'food banner16.jpg', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `food_id` varchar(10) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `timestamp` varchar(50) NOT NULL,
  `price` float DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `offer` varchar(250) DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `user_id`, `food_id`, `user_name`, `timestamp`, `price`, `quantity`, `offer`) VALUES
(3, 'VG384345', '3', '1', 'prem', '04:09:2019 12:02:06am', 99, 1, 'No'),
(13, 'VG198080', '3', '11', 'prem', '04:03:2025 03:10:20pm', 160, 2, 'No'),
(14, 'VG132404', '3', '8', 'prem', '04:03:2025 03:10:33pm', 320, 4, 'No'),
(15, 'VG824392', '3', '8', 'prem', '07:03:2025 05:38:33pm', 160, 2, 'No'),
(16, 'VG159977', '3', '8', 'prem', '07:03:2025 05:53:35pm', 80, 1, 'No'),
(25, 'VG834592', '3', '11', 'prem', '11:03:2025 03:55:50pm', 320, 4, 'No'),
(26, 'VG788204', '3', '9', 'prem', '13:03:2025 05:04:46pm', 160, 2, 'Summer Sale'),
(30, 'VG561065', '3', '9', 'prem', '13:03:2025 05:10:11pm', 140.8, 2, 'Summer Sale'),
(31, 'VG350944', '3', '1', 'prem', '13:03:2025 05:12:24pm', 89.1, 1, 'Lunch Special'),
(32, 'VG574344', '3', '9', 'prem', '19:05:2025 02:57:54pm', 80, 1, 'No'),
(33, 'VG349911', '3', '9', 'prem', '19:05:2025 02:59:03pm', 80, 1, 'No'),
(34, 'VG159489', '3', '9', 'prem', '19:05:2025 03:01:22pm', 80, 1, 'No'),
(35, 'VG422350', '3', '9', 'prem', '19:05:2025 03:09:00pm', 80, 1, 'No'),
(36, 'VG149763', '3', '9', 'prem', '19:05:2025 03:09:34pm', 192, 3, 'Weekend Discount'),
(37, 'VG403753', '3', '9', 'prem', '19:05:2025 03:12:05pm', 128, 2, 'Weekend Discount'),
(38, 'VG377533', '3', '9', 'prem', '19:05:2025 03:14:51pm', 128, 2, 'Weekend Discount'),
(39, 'VG834589', '3', '8', 'prem', '19:05:2025 04:19:58pm', 80, 1, 'No'),
(40, 'VG907646', '3', '9', 'prem', '19:05:2025 04:27:48pm', 80, 1, 'No'),
(41, 'VG233024', '47', '37', 'prem', '20:05:2025 10:23:57am', 100, 1, 'No'),
(42, 'VG102046', '47', '33', 'prem', '20:05:2025 10:24:50am', 140, 1, 'No'),
(43, 'VG262501', '47', '76', 'prem', '20:05:2025 11:33:32am', 120, 1, 'No'),
(44, 'VG327629', '47', '37', 'prem', '20:05:2025 02:33:52pm', 100, 1, 'No'),
(45, 'VG918063', '47', '39', 'prem', '07:08:2025 09:24:58am', 40, 1, 'No'),
(46, 'VG651210', '47', '32', 'prem', '07:08:2025 09:30:55am', 70, 1, 'No'),
(47, 'VG903997', '47', '1', 'prem', '24:12:2025 05:18:41pm', 99, 1, 'No'),
(48, 'VG710888', '47', '8', 'prem', '29:01:2026 09:26:32am', 160, 2, 'No'),
(49, 'VG921046', '47', '1', 'prem', '29:01:2026 09:27:14am', 198, 2, 'No'),
(50, 'VG619648', '47', '55', 'prem', '29:01:2026 09:28:39am', 90, 1, 'No'),
(51, 'VG672805', '47', '9', 'prem', '29:01:2026 09:32:31am', 160, 2, 'No'),
(52, 'VG163364', '47', '8', 'prem', '29:01:2026 09:33:32am', 80, 1, 'No'),
(53, 'VG361110', '47', '29', 'prem', '29:01:2026 09:33:55am', 280, 2, 'No'),
(54, 'VG288890', '47', '8', 'prem', '29:01:2026 09:35:13am', 72, 1, 'Lunch Special');

-- --------------------------------------------------------

--
-- Table structure for table `page_views`
--

CREATE TABLE IF NOT EXISTS `page_views` (
  `id` int(11) NOT NULL,
  `view_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `page_views`
--

INSERT INTO `page_views` (`id`, `view_count`) VALUES
(1, 2744);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `timestamp` varchar(100) DEFAULT NULL,
  `verification_code` varchar(255) DEFAULT NULL,
  `is_verified` int(10) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `timestamp`, `verification_code`, `is_verified`) VALUES
(47, 'prem agravat', 'agravatprem00@gmail.com', '12345', '19:05:2025 10:59:53am', 'db1cbcfd754ef270c38c63b917e74247', 1),
(51, 'hello', 'agravatprem777@gmail.com', '111', '08:02:2026 05:53:04pm', '1f61db96c2cc6370d99ff5c40c0c5c23', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `food`
--
ALTER TABLE `food`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_views`
--
ALTER TABLE `page_views`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `food`
--
ALTER TABLE `food`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `page_views`
--
ALTER TABLE `page_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
