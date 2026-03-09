<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SafeExternalUrl implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Allow empty/null values (nullable)
        if (empty($value)) {
            return true;
        }

        // Must be a valid URL
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Parse URL to get host
        $host = parse_url($value, PHP_URL_HOST);
        
        if (!$host) {
            return false;
        }

        // Whitelist of allowed domains for external links
        $allowedDomains = [
            'linkedin.com',
            'www.linkedin.com',
            'github.com',
            'www.github.com',
            'twitter.com',
            'www.twitter.com',
            'gitlab.com',
            'www.gitlab.com',
            'behance.net',
            'www.behance.net',
        ];

        // Check if the host matches any whitelisted domain
        foreach ($allowedDomains as $domain) {
            if ($host === $domain || substr($host, -(strlen($domain) + 1)) === '.' . $domain) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a valid URL from an allowed domain (LinkedIn, GitHub, Twitter, GitLab, or Behance).';
    }
}
