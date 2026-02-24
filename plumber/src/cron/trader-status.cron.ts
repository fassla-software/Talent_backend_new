import cron from 'node-cron';
import { Op } from 'sequelize';
import Trader, { TraderActivityStatus } from '../modules/trader/trader.model';
import Plumber, { PlumberAccountStatus } from '../modules/plumber/plumber.model';
import InspectionVisit from '../modules/inspectionVisit/inspection-visit.model';
import VisitReport from '../modules/inspectionVisit/visit-report.model';
import InspectionRequest from '../modules/inspectionRequest/inspection_request.model';
import { logStatusChange } from '../modules/statusHistory/status-history.service';
import { ClientType } from '../modules/statusHistory/status-history.model';

export const initTraderStatusCron = () => {
    // Run every week on Sunday at midnight
    cron.schedule('0 0 * * 0', async () => {
        console.log('Running trader status update cron job...');
        try {
            const traders = await Trader.findAll();

            for (const trader of traders) {
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
                    order: [['createdAt', 'DESC']],
                });

                let lastActivityDate = trader.createdAt; // Default to creation date if no visits

                if (lastVisit) {
                    lastActivityDate = lastVisit.createdAt;
                }

                const now = new Date();
                const diffTime = Math.abs(now.getTime() - lastActivityDate.getTime());
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                if (diffDays <= 30) {
                    if (trader.status !== TraderActivityStatus.ACTIVE) {
                        const oldStatus = trader.status;
                        await trader.update({ status: TraderActivityStatus.ACTIVE });
                        await logStatusChange(
                            trader.id,
                            ClientType.TRADER,
                            oldStatus,
                            TraderActivityStatus.ACTIVE
                        );
                        console.log(`Trader ${trader.id} status updated to ACTIVE`);
                    }
                } else if (diffDays > 60) {
                    // More than 2 months (approx 60 days) -> DORMANT
                    if (trader.status !== TraderActivityStatus.DORMANT) {
                        const oldStatus = trader.status;
                        await trader.update({ status: TraderActivityStatus.DORMANT });
                        await logStatusChange(
                            trader.id,
                            ClientType.TRADER,
                            oldStatus,
                            TraderActivityStatus.DORMANT
                        );
                        console.log(`Trader ${trader.id} status updated to DORMANT`);
                    }
                } else if (diffDays > 30) {
                    // More than 1 month (approx 30 days) -> INACTIVE
                    if (trader.status !== TraderActivityStatus.INACTIVE) {
                        const oldStatus = trader.status;
                        await trader.update({ status: TraderActivityStatus.INACTIVE });
                        await logStatusChange(
                            trader.id,
                            ClientType.TRADER,
                            oldStatus,
                            TraderActivityStatus.INACTIVE
                        );
                        console.log(`Trader ${trader.id} status updated to INACTIVE`);
                    }
                }
            }
            console.log('Trader status update cron job completed.');

            console.log('Running plumber status update cron job...');
            const plumbers = await Plumber.findAll();

            for (const plumber of plumbers) {
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
                    order: [['createdAt', 'DESC']],
                });

                let lastActivityDate = plumber.createdAt;

                if (lastVisit) {
                    lastActivityDate = lastVisit.createdAt;
                }

                // Also check for the last inspection request (only for plumbers)
                const lastRequest = await InspectionRequest.findOne({
                    where: { requestor_id: plumber.user_id },
                    order: [['createdAt', 'DESC']],
                });

                if (lastRequest && lastRequest.createdAt > lastActivityDate) {
                    lastActivityDate = lastRequest.createdAt;
                }

                const now = new Date();
                const diffTime = Math.abs(now.getTime() - lastActivityDate.getTime());
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                if (diffDays <= 30) {
                    if (plumber.status !== PlumberAccountStatus.ACTIVE) {
                        const oldStatus = plumber.status;
                        await plumber.update({ status: PlumberAccountStatus.ACTIVE });
                        await logStatusChange(
                            plumber.id,
                            ClientType.PLUMBER,
                            oldStatus,
                            PlumberAccountStatus.ACTIVE
                        );
                        console.log(`Plumber ${plumber.id} status updated to ACTIVE`);
                    }
                } else if (diffDays > 60) {
                    if (plumber.status !== PlumberAccountStatus.DORMANT) {
                        const oldStatus = plumber.status;
                        await plumber.update({ status: PlumberAccountStatus.DORMANT });
                        await logStatusChange(
                            plumber.id,
                            ClientType.PLUMBER,
                            oldStatus,
                            PlumberAccountStatus.DORMANT
                        );
                        console.log(`Plumber ${plumber.id} status updated to DORMANT`);
                    }
                } else if (diffDays > 30) {
                    if (plumber.status !== PlumberAccountStatus.INACTIVE) {
                        const oldStatus = plumber.status;
                        await plumber.update({ status: PlumberAccountStatus.INACTIVE });
                        await logStatusChange(
                            plumber.id,
                            ClientType.PLUMBER,
                            oldStatus,
                            PlumberAccountStatus.INACTIVE
                        );
                        console.log(`Plumber ${plumber.id} status updated to INACTIVE`);
                    }
                }
            }
            console.log('Plumber status update cron job completed.');
        } catch (error) {
            console.error('Error running trader status update cron job:', error);
        }
    });
};
