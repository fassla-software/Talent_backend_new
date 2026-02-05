import { Op } from 'sequelize';
import { getEnvoyStatistics } from '../envoy/envoy-stats.service';
import AwardService from './award.service';
import EnvoyAward from './envoy_award.model';
import Award from './award.model';

/**
 * Service to handle automatic award assignments based on performance targets
 */
export const checkAndAssignTargetAwards = async (envoyId: number) => {
    try {
        // 1. Get current month statistics
        const stats = await getEnvoyStatistics(envoyId, 'month');
        const now = new Date();
        const yearMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;

        // 2. Define the metrics to check
        const metricsToCheck = [
            {
                key: 'sales',
                title: 'Monthly Sales Target Met',
                achievement: stats.sales?.total?.amount || 0,
                target: stats.overview?.target_sales || 0,
                perfKey: 'sales'
            },
            {
                key: 'visits',
                title: 'Monthly Visits Target Met',
                achievement: stats.overview?.approved_visits || 0,
                target: stats.overview?.target_visits || 0,
                perfKey: 'visits'
            },
            {
                key: 'retention',
                title: 'Monthly Retention Target Met',
                achievement: stats.retention?.retention_rate || 0,
                target: stats.overview?.target_retention_rate || 0,
                perfKey: 'retention'
            },
            {
                key: 'conversion',
                title: 'Monthly Conversion Target Met',
                achievement: stats.conversion?.conversion_rate || 0,
                target: stats.overview?.target_conversion_rate || 0,
                perfKey: 'conversion'
            }
        ];

        for (const metric of metricsToCheck) {
            // Only proceed if a target is set and reached (>= 100%)
            if (metric.target > 0 && metric.achievement >= metric.target) {
                const autoReason = `Automatic: ${metric.key} Target Met - ${yearMonth}`;

                // Check if this award was already assigned for this month/metric
                const existing = await EnvoyAward.findOne({
                    where: {
                        envoy_id: envoyId,
                        reason: autoReason
                    }
                });

                if (!existing) {
                    // Find the award record by title
                    const award = await Award.findOne({
                        where: { title: metric.title }
                    });

                    if (award) {
                        await AwardService.assignAwardToEnvoy({
                            envoy_id: envoyId,
                            award_id: award.id,
                            reason: autoReason
                        });
                        console.log(`[AwardAutomation] Assigned "${metric.title}" to Envoy ${envoyId}`);
                    } else {
                        console.warn(`[AwardAutomation] Award title "${metric.title}" not found in database.`);
                    }
                }
            }
        }
    } catch (error) {
        console.error('[AwardAutomation] Error checking awards:', error);
    }
};
