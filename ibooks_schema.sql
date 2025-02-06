

CREATE TABLE IF NOT EXISTS `password_resets` (
  `reset_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `expires_at` DATETIME NOT NULL, -- Change TIMESTAMP to DATETIME
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reset_id`),
  KEY `user_id` (`user_id`)
);


-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--


CREATE TABLE IF NOT EXISTS `reviews` (
  `review_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `book_id` int NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  KEY `user_id` (`user_id`),
  KEY `book_id` (`book_id`)
) ;

--
-- Dumping data for table `reviews`
--



-- Insert reviews into the reviews table
INSERT INTO `reviews` (`user_id`, `book_id`, `rating`, `comment`, `created_at`) VALUES
(2, 1, 4.70, 'A powerful and moving novel about racial injustice in the Deep South.', NOW()),
(2, 2, 4.50, 'A chilling dystopian novel that explores the dangers of totalitarianism.', NOW()),
(2, 3, 4.60, 'A timeless classic about love and social class in 19th-century England.', NOW()),
(2, 4, 4.40, 'A captivating novel about the American Dream and the Jazz Age.', NOW()),
(2, 5, 4.30, 'A profound and complex novel about a whaling voyage and the pursuit of a white whale.', NOW()),
(2, 6, 4.50, 'An epic novel about Russian society during the Napoleonic Wars.', NOW()),
(2, 7, 4.20, 'A challenging and rewarding modernist novel that follows the lives of several characters in Dublin.', NOW()),
(2, 8, 4.40, 'A poignant and insightful novel about a teenager\'s struggles with identity and alienation.', NOW()),
(2, 9, 4.60, 'A masterful novel about the rise and fall of a family over several generations.', NOW()),
(2, 10, 4.80, 'An epic fantasy novel that captures the imagination with its rich world and compelling characters.', NOW()),

(2, 11, 4.70, 'A fascinating and thought-provoking history of the human species from the Stone Age to the present.', NOW()),
(2, 12, 4.60, 'An inspiring memoir about growing up in a strict Mormon family and pursuing education.', NOW()),
(2, 13, 4.50, 'A compelling book that explores the ethical issues surrounding the use of human cells in research.', NOW()),
(2, 14, 4.40, 'A valuable book that explores the strengths and contributions of introverts.', NOW()),
(2, 15, 4.60, 'A powerful book that examines the racial implications of the U.S. criminal justice system.', NOW()),
(2, 16, 4.50, 'A groundbreaking book that explains why some societies have historically been more successful than others.', NOW()),
(2, 17, 4.70, 'A heartfelt and inspiring memoir about the former First Lady\'s life and experiences.', NOW()),
(2, 18, 4.40, 'A thought-provoking book that explores the origins of the food we eat.', NOW()),
(2, 19, 4.50, 'A comprehensive and engaging book that delves into the history and future of genetics.', NOW()),
(2, 20, 4.60, 'A gripping and well-researched book about the rise and fall of Theranos.', NOW()),

(2, 21, 4.60, 'A thrilling and intricate thriller about a journalist and a hacker investigating a disappearance.', NOW()),
(2, 22, 4.50, 'A gripping and psychologically complex thriller about a missing wife and her husband.', NOW()),
(2, 23, 4.70, 'A classic and atmospheric mystery about ten strangers stranded on an isolated island.', NOW()),
(2, 24, 4.40, 'A well-crafted detective novel about a supermodel’s death.', NOW()),
(2, 25, 4.50, 'A compelling police procedural about a detective investigating a cold case.', NOW()),
(2, 26, 4.60, 'A classic and atmospheric noir detective novel featuring private eye Philip Marlowe.', NOW()),
(2, 27, 4.70, 'A classic and iconic detective novel about a valuable statuette.', NOW()),
(2, 28, 4.60, 'A timeless and engaging Sherlock Holmes novel about a family curse.', NOW()),
(2, 29, 4.50, 'The third book in the Millennium series, continuing the thrilling and complex story.', NOW()),
(2, 30, 4.40, 'A well-written and engaging detective novel about a murder at a girls’ boarding school.', NOW()),

(2, 31, 4.60, 'A timeless and beloved classic about love and social class in 19th-century England.', NOW()),
(2, 32, 4.50, 'A heart-wrenching and beautifully written novel about a woman who becomes a caregiver for a quadriplegic man.', NOW()),
(2, 33, 4.40, 'A touching and emotional love story about a couple separated by war and social class.', NOW()),
(2, 34, 4.30, 'A controversial and erotic romance novel that explores deep emotions and relationships.', NOW()),
(2, 35, 4.60, 'A captivating and imaginative time-travel romance set in 18th-century Scotland.', NOW()),
(2, 36, 4.50, 'A poignant and deeply moving novel about two teenagers with cancer falling in love.', NOW()),
(2, 37, 4.20, 'A unique and entertaining mashup of Jane Austen’s classic with zombie elements.', NOW()),
(2, 38, 4.40, 'A beautifully written and emotionally resonant novel about a man with a genetic disorder that causes him to time travel.', NOW()),
(2, 39, 4.30, 'A gripping and well-written romance novel about a tattooed biker and a college student.', NOW()),
(2, 40, 4.50, 'A powerful and emotional novel about a young woman in an abusive relationship.', NOW()),

(2, 41, 4.70, 'A masterful and epic science fiction novel about politics, religion, and ecology on a desert planet.', NOW()),
(2, 42, 4.60, 'A groundbreaking and influential cyberpunk novel about a washed-up hacker and a mysterious AI.', NOW()),
(2, 43, 4.50, 'A series of short stories that explore the fall and rise of civilizations.', NOW()),
(2, 44, 4.40, 'A fast-paced and engaging cyberpunk novel about a pizza delivery driver and a mysterious computer virus.', NOW()),
(2, 45, 4.60, 'A complex and multi-layered science fiction novel with multiple narratives and a rich plot.', NOW()),
(2, 46, 4.50, 'A thought-provoking and well-crafted novel about a human envoy on a distant planet.', NOW()),
(2, 47, 4.40, 'A hard science fiction novel that explores first contact with an alien species.', NOW()),
(2, 48, 4.70, 'A thrilling and well-written space opera about a detective and a rogue ship captain.', NOW()),
(2, 49, 4.60, 'A captivating and well-researched science fiction novel about the discovery of an alien civilization.', NOW()),
(2, 50, 4.50, 'A classic and engaging novel about a child prodigy trained to lead Earth’s defense against an alien invasion.', NOW()),

(2, 51, 4.60, 'A beautifully written and engaging fantasy novel about a gifted young man and his journey to become a wizard.', NOW()),
(2, 52, 4.70, 'The first book in the Song of Ice and Fire series, setting the stage for a epic and complex story.', NOW()),
(2, 53, 4.50, 'A beloved and timeless fantasy novel about a hobbit’s adventure to reclaim a treasure.', NOW()),
(2, 54, 4.40, 'A well-crafted and engaging fantasy novel about a group of con artists in a city of thieves.', NOW()),
(2, 55, 4.50, 'A captivating and well-written fantasy novel about a world where certain people have magical abilities.', NOW()),
(2, 56, 4.40, 'A classic and engaging fantasy novel about a magical sword and a quest to save a land from evil.', NOW()),
(2, 57, 4.50, 'A beautifully written and imaginative fantasy novel about a group of students at a secret magic school.', NOW()),
(2, 58, 4.60, 'A dark and well-crafted fantasy novel about a group of soldiers and their mission.', NOW()),
(2, 59, 4.50, 'A well-written and engaging fantasy novel about a powder mage and a political conspiracy.', NOW()),
(2, 60, 4.60, 'The second book in the Kingkiller Chronicle series, continuing the thrilling and complex story.', NOW()),

(2, 61, 4.60, 'A comprehensive and well-researched biography of the Apple co-founder.', NOW()),
(2, 62, 4.50, 'An inspiring and well-written biography of Louis Zamperini, an Olympic athlete and WWII survivor.', NOW()),
(2, 63, 4.70, 'A powerful and well-researched autobiography of the civil rights leader.', NOW()),
(2, 64, 4.70, 'A heartfelt and inspiring memoir about the former First Lady’s life and experiences.', NOW()),
(2, 65, 4.60, 'A comprehensive and well-researched biography of the Renaissance artist and inventor.', NOW()),
(2, 66, 4.50, 'A well-written and engaging biography of the influential First Lady.', NOW()),
(2, 67, 4.60, 'A well-researched and engaging biography of the tech entrepreneur.', NOW()),
(2, 68, 4.50, 'A thought-provoking and well-researched book about the ethical issues surrounding the use of human cells in research.', NOW()),
(2, 69, 4.40, 'An ancient and influential Chinese military treatise.', NOW()),
(2, 70, 4.60, 'A powerful and well-written autobiography of the South African anti-apartheid revolutionary.', NOW()),

(2, 71, 4.70, 'A classic and well-written self-help book about personal and professional effectiveness.', NOW()),
(2, 72, 4.60, 'A well-crafted and engaging book about small changes that lead to remarkable results.', NOW()),
(2, 73, 4.70, 'A profound and well-written book about finding purpose in life despite suffering.', NOW()),
(2, 74, 4.60, 'A classic and well-written motivational book about achieving wealth and success.', NOW()),
(2, 75, 4.50, 'A well-written and engaging spiritual guide to living in the present moment.', NOW()),
(2, 76, 4.70, 'A classic and well-written self-help book about interpersonal skills.', NOW()),
(2, 77, 4.60, 'A well-crafted and engaging book about waking up early to achieve your goals.', NOW()),
(2, 78, 4.50, 'A well-written and engaging book about four principles for personal freedom and happiness.', NOW()),
(2, 79, 4.60, 'A well-crafted and engaging book about vulnerability and courage.', NOW()),
(2, 80, 4.50, 'A well-written and engaging counterintuitive guide to living a good life.', NOW()),

(2, 81, 4.60, 'A well-researched and engaging history of the United States from the perspective of marginalized groups.', NOW()),
(2, 82, 4.50, 'A well-researched and engaging book that explains why some societies have historically been more successful than others.', NOW()),
(2, 83, 4.70, 'A well-researched and engaging history of the world through the lens of the Silk Roads.', NOW()),
(2, 84, 4.60, 'A comprehensive and well-researched history of the Roman Empire’s decline.', NOW()),
(2, 85, 4.50, 'A well-researched and engaging history of the first month of World War I.', NOW()),
(2, 86, 4.60, 'A comprehensive and well-researched history of World War II.', NOW()),
(2, 87, 4.50, 'A well-researched and engaging history of the Crusades from the Arab perspective.', NOW()),
(2, 88, 4.60, 'A well-researched and engaging history of the Mongol Empire and its impact on the world.', NOW()),
(2, 89, 4.70, 'A comprehensive and well-researched three-volume history of the American Civil War.', NOW()),
(2, 90, 4.60, 'A well-researched and engaging history of the Spanish conquest of the Aztec Empire.', NOW()),

(2, 91, 4.70, 'A gripping and well-written dystopian novel about a televised survival competition.', NOW()),
(2, 92, 4.80, 'A classic and well-loved novel that sets the stage for the Harry Potter series.', NOW()),
(2, 93, 4.50, 'A poignant and deeply moving novel about two teenagers with cancer falling in love.', NOW()),
(2, 94, 4.60, 'A well-crafted and engaging dystopian novel about a divided society.', NOW()),
(2, 95, 4.50, 'A well-written and engaging dystopian novel about a boy who wakes up in a maze with no memory.', NOW()),
(2, 96, 4.40, 'A well-written and engaging novel about a teenager’s experiences at a boarding school.', NOW()),
(2, 97, 4.50, 'A well-written and engaging novel about a high school student who leaves behind tapes explaining why she committed suicide.', NOW()),
(2, 98, 4.60, 'A well-written and engaging novel about a teenager navigating high school and life.', NOW()),
(2, 99, 4.50, 'A well-written and engaging novel about two misfits falling in love in the 1980s.', NOW()),
(2, 100, 4.70, 'A powerful and well-written novel about a teenager who witnesses the shooting of her friend by a police officer.', NOW());

-- --------------------------------------------------------

--
-- Table structure for table `users`
--


CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
);


--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `name`, `is_admin`, `created_at`, `last_login`) VALUES
(1, 'admin@ibooks.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 1, '2025-02-04 15:15:52', NULL),
(2, 'user@ibooks.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Regular User', 0, '2025-02-04 15:15:52', NULL),
(3, 'jama@gmail.com', '$2y$10$LBie.b7M9iOu5byPI8F5ve0s1T93hg/yH8n9YW.gl0QjQ0tzwuYxC', 'jama', 0, '2025-02-04 15:24:08', NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);
COMMIT;