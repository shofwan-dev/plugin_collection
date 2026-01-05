# Security Checklist for Production Deployment

## 🔒 Pre-Deployment Security

### Environment Configuration
- [ ] `APP_ENV=production` in `.env`
- [ ] `APP_DEBUG=false` in `.env`
- [ ] Strong `APP_KEY` generated
- [ ] All API keys moved to `.env`
- [ ] Database credentials secured
- [ ] `.env` file NOT in version control
- [ ] `.env.example` updated without sensitive data

### Code Security
- [ ] Remove all `dd()`, `dump()`, `var_dump()` statements
- [ ] Remove all test/debug routes
- [ ] No hardcoded credentials in code
- [ ] Input validation on all forms
- [ ] CSRF protection enabled
- [ ] SQL injection protection (using Eloquent/Query Builder)
- [ ] XSS protection enabled

### File Permissions
```bash
# Correct permissions
sudo chown -R www-data:www-data /var/www/cf7whatsapp-website
sudo find /var/www/cf7whatsapp-website -type d -exec chmod 755 {} \;
sudo find /var/www/cf7whatsapp-website -type f -exec chmod 644 {} \;
sudo chmod -R 775 /var/www/cf7whatsapp-website/storage
sudo chmod -R 775 /var/www/cf7whatsapp-website/bootstrap/cache
```

- [ ] Application owned by `www-data`
- [ ] Directories: 755
- [ ] Files: 644
- [ ] Storage: 775
- [ ] Bootstrap/cache: 775
- [ ] `.env` file: 600

## 🛡️ Server Security

### SSL/TLS
- [ ] SSL certificate installed (Let's Encrypt)
- [ ] HTTPS enforced (HTTP redirects to HTTPS)
- [ ] TLS 1.2+ only
- [ ] Strong cipher suites
- [ ] HSTS header enabled

### Web Server (Nginx)
- [ ] Hide server version
- [ ] Disable directory listing
- [ ] Security headers configured:
  - [ ] X-Frame-Options: SAMEORIGIN
  - [ ] X-Content-Type-Options: nosniff
  - [ ] X-XSS-Protection: 1; mode=block
  - [ ] Referrer-Policy
  - [ ] Content-Security-Policy
- [ ] Rate limiting configured
- [ ] Request size limits set
- [ ] Timeout values configured

### PHP Security
- [ ] `expose_php = Off`
- [ ] `display_errors = Off`
- [ ] `log_errors = On`
- [ ] `allow_url_fopen = Off`
- [ ] `allow_url_include = Off`
- [ ] `disable_functions` set (if needed)
- [ ] `open_basedir` restriction (optional)
- [ ] PHP version up to date

### Database Security
- [ ] MySQL secure installation completed
- [ ] Root login disabled remotely
- [ ] Dedicated database user (not root)
- [ ] Strong database password
- [ ] Database accessible only from localhost
- [ ] Regular backups configured
- [ ] Binary logging enabled (for point-in-time recovery)

### Firewall
- [ ] UFW/iptables enabled
- [ ] Only necessary ports open:
  - [ ] 22 (SSH)
  - [ ] 80 (HTTP)
  - [ ] 443 (HTTPS)
- [ ] SSH port changed (optional but recommended)
- [ ] Fail2Ban installed and configured

### SSH Security
- [ ] Password authentication disabled
- [ ] SSH key authentication only
- [ ] Root login disabled
- [ ] SSH port changed (optional)
- [ ] Fail2Ban monitoring SSH

## 🔐 Application Security

### Authentication
- [ ] Strong password requirements enforced
- [ ] Password hashing (bcrypt)
- [ ] Email verification enabled
- [ ] Rate limiting on login attempts
- [ ] Session timeout configured
- [ ] Secure session cookies (`SESSION_SECURE_COOKIE=true`)
- [ ] SameSite cookie attribute set

### Authorization
- [ ] Role-based access control (RBAC) implemented
- [ ] Admin routes protected
- [ ] API endpoints secured
- [ ] License verification endpoints protected

### API Security
- [ ] API rate limiting
- [ ] API authentication (if public API)
- [ ] Input validation
- [ ] Output sanitization
- [ ] CORS configured properly

### Payment Security (Paddle)
- [ ] Webhook signature verification
- [ ] Webhook endpoint CSRF exempt
- [ ] Production API keys (not sandbox)
- [ ] Webhook secret secured
- [ ] Transaction logging enabled

### File Upload Security
- [ ] File type validation
- [ ] File size limits
- [ ] Uploaded files stored outside public directory
- [ ] File name sanitization
- [ ] Virus scanning (optional)

## 📊 Monitoring & Logging

### Logging
- [ ] Application logs enabled
- [ ] Error logs configured
- [ ] Access logs enabled
- [ ] Log rotation configured
- [ ] Sensitive data NOT logged
- [ ] Log files protected (not web-accessible)

### Monitoring
- [ ] Server monitoring (CPU, RAM, Disk)
- [ ] Application monitoring
- [ ] Database monitoring
- [ ] SSL certificate expiry monitoring
- [ ] Uptime monitoring
- [ ] Error tracking (Sentry, Bugsnag, etc.)

### Backups
- [ ] Automated daily backups
- [ ] Database backups
- [ ] File backups (storage, uploads)
- [ ] Backup verification
- [ ] Off-site backup storage
- [ ] Backup retention policy (7-30 days)
- [ ] Backup restoration tested

## 🔄 Maintenance

### Updates
- [ ] System packages updated regularly
- [ ] PHP updated to latest stable
- [ ] Laravel updated to latest LTS
- [ ] Composer dependencies updated
- [ ] Security patches applied promptly

### Security Audits
- [ ] Regular security scans
- [ ] Dependency vulnerability checks (`composer audit`)
- [ ] Code security review
- [ ] Penetration testing (optional)

### Incident Response
- [ ] Incident response plan documented
- [ ] Contact information updated
- [ ] Backup admin access configured
- [ ] Recovery procedures tested

## 🚨 Emergency Procedures

### If Site is Compromised
1. Take site offline immediately
2. Change all passwords (database, admin, SSH)
3. Review access logs
4. Restore from clean backup
5. Update all dependencies
6. Scan for malware
7. Review and fix vulnerability
8. Monitor closely after restoration

### If Database is Compromised
1. Take database offline
2. Change database credentials
3. Restore from backup
4. Review database logs
5. Audit database users and permissions
6. Update application database config

## ✅ Final Checklist

Before going live:
- [ ] All security items above checked
- [ ] Application tested thoroughly
- [ ] Backups working and tested
- [ ] Monitoring configured
- [ ] SSL certificate valid
- [ ] DNS configured correctly
- [ ] Email sending working
- [ ] Payment gateway tested
- [ ] Admin panel accessible
- [ ] Customer dashboard working
- [ ] License activation tested
- [ ] Webhook endpoints tested
- [ ] Error pages customized
- [ ] Terms of Service updated
- [ ] Privacy Policy updated
- [ ] Contact information updated

## 📞 Emergency Contacts

```
Server Provider: _______________
Domain Registrar: _______________
SSL Provider: Let's Encrypt
Database Admin: _______________
System Admin: _______________
Developer: _______________
```

## 🔗 Useful Commands

### Check Security
```bash
# Check open ports
sudo netstat -tulpn

# Check firewall status
sudo ufw status

# Check failed login attempts
sudo grep "Failed password" /var/log/auth.log

# Check SSL certificate
openssl s_client -connect yourdomain.com:443 -servername yourdomain.com

# Check file permissions
find /var/www/cf7whatsapp-website -type f -perm 777
```

### Security Scan
```bash
# Check for malware (install rkhunter first)
sudo rkhunter --check

# Check for rootkits (install chkrootkit first)
sudo chkrootkit

# Audit composer dependencies
composer audit
```

---

**Last Updated:** January 2026
**Review Date:** Every 3 months
