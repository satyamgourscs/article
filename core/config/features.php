<?php

/**
 * Legacy freelancer bidding (old jobs/bids/projects UI and routes).
 * When false, the job portal (posted_jobs / job_applications) is the primary flow.
 */
return [
    'legacy_bidding' => filter_var(env('LEGACY_BIDDING_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
];
