import cron from 'node-cron';
import { Op } from 'sequelize';
import Trader, { TraderActivityStatus } from '../modules/trader/trader.model';
import Plumber, { PlumberAccountStatus } from '../modules/plumber/plumber.model';
import InspectionVisit from '../modules/inspectionVisit/inspection-visit.model';
import VisitReport from '../modules/inspectionVisit/visit-report.model';

export const initTraderStatusCron = () => {
    // Run every minute (for testing)
    cron.schedule('* * * * *', async () => {
        console.log('Running trader status update cron job...');
        try {
            const activeTraders = await Trader.findAll({
                where: {
                    status: {
                        [Op.ne]: TraderActivityStatus.DORMANT,
                    },
                },
            });

            for (const trader of activeTraders) {
                // Find the last visit with sales_value for this trader
                const lastVisit = await InspectionVisit.findOne({
                    where: {
                        trader_id: trader.id,
                    },
                    include: [
                        {
                            model: VisitReport,
                            as: 'visitReport',
                            where: {
                                sales_value: {
                                    [Op.gt]: 0, // Assuming sales_value > 0 means it has a value
                                },
                            },
                            required: true,
                        },
                    ],
                    order: [['InspectionVisit', 'createdAt', 'DESC']],
                });

                let lastActivityDate = trader.createdAt; // Default to creation date if no visits

                if (lastVisit) {
                    lastActivityDate = lastVisit.createdAt;
                }

                const now = new Date();
                const diffTime = Math.abs(now.getTime() - lastActivityDate.getTime());
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                if (diffDays > 60) {
                    // More than 2 months (approx 60 days) -> DORMANT
                    await trader.update({ status: TraderActivityStatus.DORMANT });
                    console.log(`Trader ${trader.id} status updated to DORMANT`);
                } else if (diffDays > 30) {
                    // More than 1 month (approx 30 days) -> INACTIVE
                    await trader.update({ status: TraderActivityStatus.INACTIVE });
                    console.log(`Trader ${trader.id} status updated to INACTIVE`);
                }
            }
            console.log('Trader status update cron job completed.');

            console.log('Running plumber status update cron job...');
            const activePlumbers = await Plumber.findAll({
                where: {
                    status: {
                        [Op.ne]: PlumberAccountStatus.DORMANT,
                    },
                },
            });

            for (const plumber of activePlumbers) {
                // Find the last visit with sales_value for this plumber
                const lastVisit = await InspectionVisit.findOne({
                    where: {
                        plumber_id: plumber.id,
                    },
                    include: [
                        {
                            model: VisitReport,
                            as: 'visitReport',
                            where: {
                                sales_value: {
                                    [Op.gt]: 0,
                                },
                            },
                            required: true,
                        },
                    ],
                    order: [['InspectionVisit', 'createdAt', 'DESC']],
                });

                let lastActivityDate = plumber.createdAt;

                if (lastVisit) {
                    lastActivityDate = lastVisit.createdAt;
                }

                const now = new Date();
                const diffTime = Math.abs(now.getTime() - lastActivityDate.getTime());
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                if (diffDays > 60) {
                    await plumber.update({ status: PlumberAccountStatus.DORMANT });
                    console.log(`Plumber ${plumber.id} status updated to DORMANT`);
                } else if (diffDays > 30) {
                    await plumber.update({ status: PlumberAccountStatus.INACTIVE });
                    console.log(`Plumber ${plumber.id} status updated to INACTIVE`);
                }
            }
            console.log('Plumber status update cron job completed.');
        } catch (error) {
            console.error('Error running trader status update cron job:', error);
        }
    });
};
