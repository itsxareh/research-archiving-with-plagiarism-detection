<style>
.footer-container {
  background-color: #333;
  color: #fff;
  padding: 3rem 0 1rem;
  width: 100%;
  overflow-x: hidden;
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  padding: 0 2rem;
  box-sizing: border-box;
}

.footer-section {
  flex: 1;
  min-width: 250px;
  margin-bottom: 2rem;
  padding-right: 2rem;
}

.footer-section h4 {
  color: #fff;
  font-size: 1.2rem;
  margin-bottom: 1rem;
  font-weight: 600;
}
.footer-section img {
  width: 16px;
  height: 16px;
  margin-right: 8px;
  vertical-align: middle;
  filter: invert(1);
}
.footer-section p {
  color: #ccc;
  line-height: 1.6;
}

.footer-section ul {
  list-style: none;
  padding: 0;
}

.footer-section ul li {
  margin-bottom: 0.5rem;
}

.footer-section ul li a {
  color: #ccc;
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-section ul li a:hover {
  color: #fff;
}

.footer-section i {
  margin-right: 0.5rem;
  color: #a33333;
}

.footer-bottom {
  text-align: center;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid #444;
}

.footer-bottom p {
  color: #888;
  font-size: 0.9rem;
}

@media (max-width: 768px) {
  .footer-section {
      flex: 100%;
      margin-bottom: 2rem;
      padding-right: 0;
  }
  
  .footer-content {
      flex-direction: column;
  }
}
</style>
<footer class="footer-container">
    <div class="footer-content">
        <div class="footer-section">
            <h4>Research Repository</h4>
            <p>Your trusted platform for archiving and managing research papers.</p>
        </div>
        
        <div class="footer-section">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="../student/login.php">Dashboard</a></li>
                <li><a href="../student/all_project_list.php">Research Papers</a></li>
                <li><a href="../about.php">About Us</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h4>Contact</h4>
            <ul>
                <li><img src="../../images/email.svg" alt="email"> support@earistmnlrepository.com</li>
                <li><img src="../../images/location.svg" alt="location"> EARIST, Manila</li>
            </ul>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Research Repository. All rights reserved.</p>
    </div>
</footer>