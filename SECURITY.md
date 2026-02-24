# Security Guidelines for CAPDEVhub

## Super Admin Account Security

### Current Implementation

The Super Admin account is created via a database seeder. For security, the seeder now supports environment variables.

### Recommended Setup

1. **Set credentials in `.env` file** (never commit this file to version control):

```env
SUPER_ADMIN_EMAIL=superadmin@capdevhub.dilg-ncr.gov.ph
SUPER_ADMIN_USERNAME=Super Administrator
SUPER_ADMIN_PASSWORD=YourSecurePasswordHere123!
```

2. **Run the seeder**:
```bash
php artisan db:seed --class=SuperAdminSeeder
```

### Security Best Practices

✅ **DO:**
- Set `SUPER_ADMIN_PASSWORD` in `.env` file
- Use a strong, unique password (minimum 16 characters)
- Change the password immediately after first login
- Keep `.env` file out of version control (already in `.gitignore`)
- Use different passwords for different environments (dev, staging, production)
- Rotate passwords regularly

❌ **DON'T:**
- Hardcode passwords in code
- Commit `.env` file to Git
- Share passwords via insecure channels
- Use the same password across environments
- Leave default passwords unchanged

### If Password is Not Set

If `SUPER_ADMIN_PASSWORD` is not set in `.env`, the seeder will:
- Generate a random secure password
- Display it **once** in the console
- **Save it immediately** - it won't be shown again

### Password Requirements

- Minimum 8 characters (recommended: 16+)
- Mix of uppercase, lowercase, numbers, and special characters
- Not based on dictionary words
- Unique to this application

### Additional Security Measures

1. **Two-Factor Authentication (Future Enhancement)**
   - Consider implementing 2FA for Super Admin accounts

2. **Password Expiration**
   - Force password change on first login
   - Implement password expiration policy

3. **Audit Logging**
   - Log all Super Admin actions
   - Monitor login attempts

4. **IP Restrictions (Optional)**
   - Restrict Super Admin access to specific IP addresses

## Environment Variables

Add these to your `.env` file:

```env
# Super Admin Credentials (REQUIRED for production)
SUPER_ADMIN_EMAIL=superadmin@capdevhub.dilg-ncr.gov.ph
SUPER_ADMIN_USERNAME=Super Administrator
SUPER_ADMIN_PASSWORD=YourSecurePasswordHere123!
```

## Production Deployment Checklist

- [ ] Set `SUPER_ADMIN_PASSWORD` in production `.env`
- [ ] Verify `.env` is not in version control
- [ ] Change default password after first login
- [ ] Enable 2FA if available
- [ ] Set up audit logging
- [ ] Configure IP restrictions if needed
- [ ] Document password in secure password manager
- [ ] Limit access to Super Admin credentials
