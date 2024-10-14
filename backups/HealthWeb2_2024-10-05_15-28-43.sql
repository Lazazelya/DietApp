-- MySQL dump 10.13  Distrib 8.0.39, for Win64 (x86_64)
--
-- Host: localhost    Database: HealthWeb2
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `calorie_tracker`
--

DROP TABLE IF EXISTS `calorie_tracker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calorie_tracker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calories` decimal(10,2) DEFAULT NULL,
  `calories_burned` int(11) DEFAULT NULL,
  `calories_consumed` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `calorie_tracker_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `food_products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `calorie_tracker_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calorie_tracker`
--

LOCK TABLES `calorie_tracker` WRITE;
/*!40000 ALTER TABLE `calorie_tracker` DISABLE KEYS */;
INSERT INTO `calorie_tracker` VALUES (1,NULL,1450,NULL,'2024-09-29 16:16:40',NULL,5),(2,NULL,2400,NULL,'2024-09-29 16:17:59',NULL,2),(3,NULL,500,NULL,'2024-10-05 09:53:47',NULL,7),(10,890.00,NULL,NULL,'2024-10-05 10:42:26',81,8),(24,19990.00,NULL,NULL,'2024-10-05 12:47:13',162,2),(27,0.47,NULL,NULL,'2024-10-05 13:04:53',82,2);
/*!40000 ALTER TABLE `calorie_tracker` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `food_products`
--

DROP TABLE IF EXISTS `food_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `food_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `calories_per_100g` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `food_products`
--

LOCK TABLES `food_products` WRITE;
/*!40000 ALTER TABLE `food_products` DISABLE KEYS */;
INSERT INTO `food_products` VALUES (81,'Банан',89.00,'2024-09-29 15:43:44'),(82,'Апельсин',47.00,'2024-09-29 15:43:44'),(83,'Клубника',32.00,'2024-09-29 15:43:44'),(84,'Виноград',69.00,'2024-09-29 15:43:44'),(85,'Ананас',50.00,'2024-09-29 15:43:44'),(86,'Манго',60.00,'2024-09-29 15:43:44'),(87,'Черника',57.00,'2024-09-29 15:43:44'),(88,'Арбуз',30.00,'2024-09-29 15:43:44'),(89,'Персик',39.00,'2024-09-29 15:43:44'),(90,'Слива',46.00,'2024-09-29 15:43:44'),(91,'Вишня',50.00,'2024-09-29 15:43:44'),(92,'Киви',61.00,'2024-09-29 15:43:44'),(93,'Груша',57.00,'2024-09-29 15:43:44'),(94,'Огурец',16.00,'2024-09-29 15:43:44'),(95,'Помидор',18.00,'2024-09-29 15:43:44'),(96,'Морковь',41.00,'2024-09-29 15:43:44'),(97,'Брокколи',34.00,'2024-09-29 15:43:44'),(98,'Шпинат',23.00,'2024-09-29 15:43:44'),(99,'Салат',15.00,'2024-09-29 15:43:44'),(100,'Картофель',77.00,'2024-09-29 15:43:44'),(101,'Сладкий картофель',86.00,'2024-09-29 15:43:44'),(102,'Баклажан',25.00,'2024-09-29 15:43:44'),(103,'Кабачок',17.00,'2024-09-29 15:43:44'),(104,'Тыква',26.00,'2024-09-29 15:43:44'),(105,'Болгарский перец',20.00,'2024-09-29 15:43:44'),(106,'Лук',40.00,'2024-09-29 15:43:44'),(107,'Чеснок',149.00,'2024-09-29 15:43:44'),(108,'Грибы',22.00,'2024-09-29 15:43:44'),(109,'Зелёная фасоль',31.00,'2024-09-29 15:43:44'),(110,'Авокадо',160.00,'2024-09-29 15:43:44'),(111,'Куриная грудка',165.00,'2024-09-29 15:43:44'),(112,'Говяжий стейк',271.00,'2024-09-29 15:43:44'),(113,'Свиная отбивная',242.00,'2024-09-29 15:43:44'),(114,'Лосось',208.00,'2024-09-29 15:43:44'),(115,'Тунец',132.00,'2024-09-29 15:43:44'),(116,'Креветки',99.00,'2024-09-29 15:43:44'),(117,'Яйцо',155.00,'2024-09-29 15:43:44'),(118,'Сыр чеддер',402.00,'2024-09-29 15:43:44'),(119,'Сыр моцарелла',280.00,'2024-09-29 15:43:44'),(120,'Молоко (цельное)',61.00,'2024-09-29 15:43:44'),(121,'Йогурт (натуральный)',59.00,'2024-09-29 15:43:44'),(122,'Масло сливочное',717.00,'2024-09-29 15:43:44'),(123,'Оливковое масло',884.00,'2024-09-29 15:43:44'),(124,'Миндаль',579.00,'2024-09-29 15:43:44'),(125,'Грецкие орехи',654.00,'2024-09-29 15:43:44'),(126,'Арахис',567.00,'2024-09-29 15:43:44'),(127,'Кешью',553.00,'2024-09-29 15:43:44'),(128,'Хлеб (цельнозерновой)',247.00,'2024-09-29 15:43:44'),(129,'Рис (белый)',130.00,'2024-09-29 15:43:44'),(130,'Паста (отварная)',131.00,'2024-09-29 15:43:44'),(131,'Овсянка',389.00,'2024-09-29 15:43:44'),(132,'Кукурузные хлопья',357.00,'2024-09-29 15:43:44'),(133,'Шоколад (горький)',546.00,'2024-09-29 15:43:44'),(134,'Мёд',304.00,'2024-09-29 15:43:44'),(135,'Джем',250.00,'2024-09-29 15:43:44'),(136,'Арахисовое масло',588.00,'2024-09-29 15:43:44'),(137,'Мороженое (ванильное)',207.00,'2024-09-29 15:43:44'),(138,'Кока-кола',42.00,'2024-09-29 15:43:44'),(139,'Апельсиновый сок',45.00,'2024-09-29 15:43:44'),(140,'Кофе (чёрный)',2.00,'2024-09-29 15:43:44'),(141,'Чай (чёрный)',1.00,'2024-09-29 15:43:44'),(142,'Пиво',43.00,'2024-09-29 15:43:44'),(143,'Вино (красное)',85.00,'2024-09-29 15:43:44'),(144,'Виски',250.00,'2024-09-29 15:43:44'),(145,'Пицца (пепперони)',266.00,'2024-09-29 15:43:44'),(146,'Бургер',295.00,'2024-09-29 15:43:44'),(147,'Картофель фри',312.00,'2024-09-29 15:43:44'),(148,'Хот-дог',290.00,'2024-09-29 15:43:44'),(149,'Лазанья',135.00,'2024-09-29 15:43:44'),(150,'Суши',140.00,'2024-09-29 15:43:44'),(151,'Тако',226.00,'2024-09-29 15:43:44'),(152,'Фалафель',333.00,'2024-09-29 15:43:44'),(153,'Хумус',166.00,'2024-09-29 15:43:44'),(154,'Бекон',541.00,'2024-09-29 15:43:44'),(155,'Сосиски',301.00,'2024-09-29 15:43:44'),(156,'Куриные наггетсы',296.00,'2024-09-29 15:43:44'),(157,'Блины',227.00,'2024-09-29 15:43:44'),(158,'Вафли',291.00,'2024-09-29 15:43:44'),(159,'яблоко',88.00,'2024-09-29 15:44:07'),(161,'Молоко',80.00,'2024-10-03 17:53:13'),(162,'СуперФуд',19990.00,'2024-10-05 12:46:37');
/*!40000 ALTER TABLE `food_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'user'),(2,'admin'),(3,'trainer');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_executions`
--

DROP TABLE IF EXISTS `training_executions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `training_executions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `execution_date` datetime DEFAULT current_timestamp(),
  `training_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `training_id` (`training_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `training_executions_ibfk_1` FOREIGN KEY (`training_id`) REFERENCES `training_programs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `training_executions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_executions`
--

LOCK TABLES `training_executions` WRITE;
/*!40000 ALTER TABLE `training_executions` DISABLE KEYS */;
INSERT INTO `training_executions` VALUES (1,'2024-09-29 19:16:40',5,5),(2,'2024-09-29 19:17:15',3,5),(3,'2024-09-29 19:17:59',3,2),(4,'2024-09-29 19:18:04',1,2),(5,'2024-09-29 19:18:15',5,2),(6,'2024-09-29 19:18:19',4,2),(7,'2024-10-05 12:53:47',1,7);
/*!40000 ALTER TABLE `training_executions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_programs`
--

DROP TABLE IF EXISTS `training_programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `training_programs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `calories_burned` int(11) DEFAULT NULL,
  `training_name` varchar(255) DEFAULT NULL,
  `training_type_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `training_type_id` (`training_type_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `training_programs_ibfk_1` FOREIGN KEY (`training_type_id`) REFERENCES `training_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `training_programs_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_programs`
--

LOCK TABLES `training_programs` WRITE;
/*!40000 ALTER TABLE `training_programs` DISABLE KEYS */;
INSERT INTO `training_programs` VALUES (1,'Силовая для начинающих','Оптимально делать упражнения по 3 подхода, с отдыхом 1-2 минуты между ними.\r\nРазминка на кардиотренажере и суставная гимнастика – 15 минут; \r\nПланка – 0,5 минуты;\r\nВыпады без веса – по 10 повторений на каждую сторону; \r\nВертикальная тяга в тренажере – 10 повторений; \r\nГоризонтальная тяга –10 повторений; \r\nЖим ногами – 15 повторений; \r\nЖим или разведения в тренажере «Бабочка» – 10 повторений; \r\nПланка – 0,5 минуты; \r\nРастяжка – 15 минут.\r\n',35,500,NULL,1,3,'2024-09-29 15:52:39'),(3,'Силовая 1','Оптимально делать упражнения по 3 подхода, с отдыхом 1-2 минуты между ними.\r\nРазминка на кардиотренажере и суставная гимнастика – 15 минут; \r\nПланка – 3 минуты;\r\nВыпады без веса – по 20 повторений на каждую сторону; \r\nВертикальная тяга в тренажере – 20 повторений; \r\nГоризонтальная тяга –20 повторений; \r\nЖим ногами – 15 повторений; \r\nЖим или разведения в тренажере «Бабочка» – 10 повторений; \r\nПланка – 0,5 минуты; \r\nРастяжка – 15 минут.\r\nОптимально делать упражнения по 3 подхода, с отдыхом 1-2 минуты между ними.\r\nРазминка на кардиотренажере и суставная гимнастика – 15 минут; \r\nПланка – 0,5 минуты;\r\nВыпады без веса – по 10 повторений на каждую сторону; \r\nВертикальная тяга в тренажере – 10 повторений; \r\nГоризонтальная тяга –20 повторений; \r\nЖим ногами – 15 повторений; \r\nЖим или разведения в тренажере «Бабочка» – 10 повторений; \r\nПланка – 0,5 минуты; \r\nРастяжка – 15 минут.\r\nОптимально делать упражнения по 3 подхода, с отдыхом 1-2 минуты между ними.\r\nРазминка на кардиотренажере и суставная гимнастика – 15 минут; \r\nПланка – 0,5 минуты;\r\nВыпады без веса – по 10 повторений на каждую сторону; \r\nВертикальная тяга в тренажере – 10 повторений; \r\nГоризонтальная тяга –10 повторений; \r\nЖим ногами – 15 повторений; \r\nЖим или разведения в тренажере «Бабочка» – 10 повторений; \r\nПланка – 0,5 минуты; \r\nРастяжка – 15 минут.\r\n',120,800,NULL,1,3,'2024-09-29 15:55:55'),(4,'Пилатесс','Пилатес',30,450,NULL,3,4,'2024-09-29 15:57:02'),(5,'Интервальная','Интервальная',60,650,NULL,2,4,'2024-09-29 15:57:45');
/*!40000 ALTER TABLE `training_programs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_types`
--

DROP TABLE IF EXISTS `training_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `training_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_types`
--

LOCK TABLES `training_types` WRITE;
/*!40000 ALTER TABLE `training_types` DISABLE KEYS */;
INSERT INTO `training_types` VALUES (1,'Силовая'),(2,'HIIT'),(3,'Пилатес');
/*!40000 ALTER TABLE `training_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_workouts`
--

DROP TABLE IF EXISTS `user_workouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_workouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calories_burned` int(11) DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `training_program_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `training_program_id` (`training_program_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_workouts_ibfk_1` FOREIGN KEY (`training_program_id`) REFERENCES `training_programs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_workouts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_workouts`
--

LOCK TABLES `user_workouts` WRITE;
/*!40000 ALTER TABLE `user_workouts` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_workouts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `weight` float DEFAULT NULL,
  `height` float DEFAULT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `activity_level` enum('sedentary','light','moderate','active','very_active') DEFAULT NULL,
  `daily_calories` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Админ','lizzygen03@gmail.com','$2y$10$piJQElGxwRaGmNmh12JdhuPunfAEK6BTwh.MSC/nH.iWlXbZZFqaW',NULL,NULL,NULL,NULL,NULL,NULL,'2024-09-29 15:33:40',2),(2,'Лиза','user@gmail.com','$2y$10$2awncqjBbqNjcRpZfBOgEOJhaw0R4VoGa66v3tsoc8EoTNwRmkUvS',20,60,167,'female','active',19990.47,'2024-09-29 15:34:34',1),(3,'Jaklin','trainer@gmail.com','$2y$10$VI9G8GqIyGrN0vfpcnLXie5/zWpzf8pJ/H2YT1.bEMpMO5vbrCKVO',NULL,NULL,NULL,NULL,NULL,NULL,'2024-09-29 15:34:56',3),(4,'Настюха','trainer2@gmail.com','$2y$10$MT3e3TnwNZbg5xiak/OtkuwMDswiAy97VlpdUsiy8QbBS59LCKafm',NULL,NULL,NULL,NULL,NULL,NULL,'2024-09-29 15:35:08',3),(5,'Тест','test@test.by','$2y$10$xWiYOYCvkTHnj4d14WOOwOhNoPtfcUDJYpJAFpS6F.02UQaJy7.nO',17,55,175,'male','sedentary',NULL,'2024-09-29 16:15:46',1),(7,'RGYETH','sabdabsd@gmail.com','$2y$10$O1n6iOVQqf2nz56Hsx5X6O0PPbaYfN.PfNi/.vMC7bMEkGChV5Nr6',99,159,200,'male','sedentary',NULL,'2024-10-05 09:52:18',1),(8,'WW','T@T.BY','$2y$10$tpdbYYSS7NXX/sERoxQhK.UH.saSWFHOEp1AO0M02fu.qYPpTj7MK',1,1,1,'male','sedentary',99999999.99,'2024-10-05 10:16:48',1),(9,'test','N@g.by','$2y$10$98J7UOolStFPlgkI0GaejOLPUbkb/tYI6D2ZyIqfu4S0/UNJumO0G',2147483647,3.40282e38,123,'male','active',NULL,'2024-10-05 13:08:22',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-05 16:28:44
