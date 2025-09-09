-- Create database
CREATE DATABASE IF NOT EXISTS technova_db;
USE technova_db;

-- Create admins table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create news table
CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    category ENUM('AI', 'Gadgets', 'Programming', 'Startups', 'Cybersecurity') NOT NULL,
    author VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create newsletter_subscribers table
CREATE TABLE newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create contact_messages table
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample admin users (password: password123)
INSERT INTO admins (username, email, password, role) VALUES
('superadmin', 'super@technews.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin'),
('admin1', 'admin1@technews.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('admin2', 'admin2@technews.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('admin3', 'admin3@technews.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('admin4', 'admin4@technews.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('admin5', 'admin5@technews.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample news articles
INSERT INTO news (title, content, image, category, author) VALUES
('The Future of AI in Everyday Life', 'Artificial Intelligence is rapidly transforming how we interact with technology. From smart assistants to predictive algorithms, AI is becoming an integral part of our daily routines. This article explores the current state of AI technology and its potential future applications in various industries including healthcare, education, and transportation. We discuss the ethical considerations and how businesses are adapting to this new technological landscape.', 'ai-future.jpg', 'AI', 'Jane Smith'),
('Top 10 Gadgets of 2023', 'This year has seen some incredible technological advancements in consumer electronics. From foldable smartphones to advanced wearable health monitors, the gadget landscape is evolving at an unprecedented pace. Our team has tested hundreds of products to bring you this definitive list of the most innovative and useful gadgets available today. Each product is evaluated based on design, functionality, and value for money.', 'gadgets-2023.jpg', 'Gadgets', 'Mike Johnson'),
('Getting Started with Python Programming', 'Python continues to be one of the most popular programming languages for beginners and experts alike. Its simple syntax and powerful libraries make it ideal for a wide range of applications from web development to data science. This comprehensive guide covers everything you need to know to start your Python journey, including setting up your development environment, understanding basic syntax, and working with popular frameworks like Django and Flask.', 'python.jpg', 'Programming', 'Sarah Williams'),
('Cybersecurity Threats to Watch in 2023', 'As technology evolves, so do the threats to our digital security. This year has seen a significant increase in sophisticated cyber attacks targeting both individuals and organizations. From ransomware to phishing schemes, this article details the most pressing cybersecurity threats of 2023 and provides practical advice on how to protect yourself and your business. We also discuss emerging trends in cybersecurity defense and what to expect in the coming years.', 'cybersecurity.jpg', 'Cybersecurity', 'Robert Chen'),
('Startup Funding Trends in the Tech Industry', 'The startup ecosystem has seen significant changes in funding patterns over the past year. Venture capital firms are becoming more selective, while alternative funding sources are gaining popularity. This analysis examines current trends in tech startup funding, including the rise of angel investing platforms, the impact of economic conditions on valuation, and which sectors are attracting the most investment. We also provide tips for founders seeking funding in the current climate.', 'startup-funding.jpg', 'Startups', 'Emily Davis'),
('The Rise of Quantum Computing', 'Quantum computing is moving from theoretical concept to practical reality faster than many experts predicted. This technology promises to revolutionize fields from cryptography to drug discovery by solving problems that are currently intractable for classical computers. This article explores the current state of quantum computing, the major players in the industry, and the potential implications for various sectors. We also discuss the challenges that remain before quantum computers become widely accessible.', 'quantum-computing.jpg', 'AI', 'David Wilson'),
('Smart Home Technology: What''s New in 2023', 'The smart home market continues to expand with new products and integrations that make homes more efficient, secure, and comfortable. This year has seen significant advancements in interoperability between devices, energy management systems, and AI-powered home assistants. Our review covers the latest innovations in smart home technology, including new products from leading brands, emerging standards, and practical tips for creating a cohesive smart home ecosystem.', 'smart-home.jpg', 'Gadgets', 'Lisa Anderson'),
('Web Development Frameworks Comparison', 'Choosing the right web development framework can significantly impact the success of your project. With so many options available, from React and Vue.js to Angular and Svelte, it can be challenging to determine which is best for your needs. This comprehensive comparison examines the most popular front-end and back-end frameworks, their strengths and weaknesses, performance characteristics, and ideal use cases. We also discuss emerging trends in web development for 2023.', 'web-frameworks.jpg', 'Programming', 'Michael Brown'),
('Data Privacy Regulations Update', 'Data privacy regulations continue to evolve around the world, creating new challenges and opportunities for businesses. From GDPR in Europe to CCPA in California, companies must navigate a complex landscape of compliance requirements. This article provides an update on the latest developments in data privacy regulations, including new laws coming into effect, enforcement trends, and best practices for maintaining compliance while still delivering excellent customer experiences.', 'data-privacy.jpg', 'Cybersecurity', 'Jennifer Lee'),
('The Future of Remote Work Technology', 'The shift to remote work has accelerated the development of new technologies designed to support distributed teams. From advanced collaboration tools to virtual office environments, the remote work tech landscape is rapidly evolving. This article explores the latest innovations in remote work technology, including AI-powered meeting assistants, virtual reality workspaces, and advanced security solutions for distributed organizations. We also discuss what the future might hold for the way we work.', 'remote-work.jpg', 'Startups', 'Kevin Martinez');

-- Insert sample newsletter subscribers
INSERT INTO newsletter_subscribers (email) VALUES
('john.doe@example.com'),
('jane.smith@example.com'),
('mike.johnson@example.com'),
('sarah.williams@example.com'),
('robert.chen@example.com'),
('emily.davis@example.com'),
('david.wilson@example.com'),
('lisa.anderson@example.com'),
('michael.brown@example.com'),
('jennifer.lee@example.com'),
('kevin.martinez@example.com');

-- Insert sample contact messages
INSERT INTO contact_messages (name, email, message) VALUES
('Alice Green', 'alice.green@example.com', 'Hello, I would like to know more about your services.'),
('Bob White', 'bob.white@example.com', 'I have a question regarding my subscription.'),
('Charlie Black', 'charlie.black@example.com', 'Can you provide more details about your pricing?'),
('Diana Prince', 'diana.prince@example.com', 'I am interested in collaborating with your team.'),
('Ethan Hunt', 'ethan.hunt@example.com', 'What is the process for submitting a news article?');
