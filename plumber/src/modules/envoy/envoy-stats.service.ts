import { Op } from 'sequelize';
import Trader, { TraderActivityStatus } from '../trader/trader.model';
import Plumber, { PlumberAccountStatus } from '../plumber/plumber.model';
import VisitReport from '../inspectionVisit/visit-report.model';
import InspectionVisit, { VisitStatus } from '../inspectionVisit/inspection-visit.model';
import EnvoySetting from './envoy.model';
import ClientStatusHistory, { ClientType } from '../statusHistory/status-history.model';

export type TimePeriodType = 'week' | 'month' | 'quarter' | 'year';

interface TimePeriod {
    start_date: Date;
    end_date: Date;
    type: TimePeriodType;
}

/**
 * Calculate time period boundaries
 */
export const getTimePeriod = (type: TimePeriodType, date: Date = new Date()): TimePeriod => {
    const result: TimePeriod = {
        start_date: new Date(date),
        end_date: new Date(date),
        type,
    };

    switch (type) {
        case 'week': {
            // Week: Saturday to Friday
            const day = date.getDay(); // 0 = Sunday, 6 = Saturday
            const diff = day === 6 ? 0 : day + 1;
            const saturday = new Date(date);
            saturday.setDate(date.getDate() - diff);
            saturday.setHours(0, 0, 0, 0);

            const friday = new Date(saturday);
            friday.setDate(saturday.getDate() + 6);
            friday.setHours(23, 59, 59, 999);

            result.start_date = saturday;
            result.end_date = friday;
            break;
        }

        case 'month': {
            const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            firstDay.setHours(0, 0, 0, 0);

            const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
            lastDay.setHours(23, 59, 59, 999);

            result.start_date = firstDay;
            result.end_date = lastDay;
            break;
        }

        case 'quarter': {
            const quarter = Math.floor(date.getMonth() / 3);
            const firstMonth = quarter * 3;
            const firstDay = new Date(date.getFullYear(), firstMonth, 1);
            firstDay.setHours(0, 0, 0, 0);

            const lastDay = new Date(date.getFullYear(), firstMonth + 3, 0);
            lastDay.setHours(23, 59, 59, 999);

            result.start_date = firstDay;
            result.end_date = lastDay;
            break;
        }

        case 'year': {
            const firstDay = new Date(date.getFullYear(), 0, 1);
            firstDay.setHours(0, 0, 0, 0);

            const lastDay = new Date(date.getFullYear(), 11, 31);
            lastDay.setHours(23, 59, 59, 999);

            result.start_date = firstDay;
            result.end_date = lastDay;
            break;
        }
    }

    return result;
};

/**
 * Calculate conversion stats - clients who became ACTIVE during period
 */
export const getConversionStats = async (
    inspectorId: number,
    period: TimePeriod
) => {
    // Find all status changes TO ACTIVE within the period
    const conversions = await ClientStatusHistory.findAll({
        where: {
            new_status: {
                [Op.in]: [TraderActivityStatus.ACTIVE, PlumberAccountStatus.ACTIVE],
            },
            old_status: {
                [Op.or]: [
                    { [Op.ne]: TraderActivityStatus.ACTIVE },
                    { [Op.is]: null }
                ]
            },
            changed_at: {
                [Op.between]: [period.start_date, period.end_date],
            },
        },
        include: [
            {
                model: Trader,
                as: 'trader',
                where: { inspector_id: inspectorId },
                required: false,
            },
            {
                model: Plumber,
                as: 'plumber',
                where: { inspector_id: inspectorId },
                required: false,
            },
        ],
    });

    // Filter for this inspector's clients
    const inspectorConversions = conversions.filter(
        conv =>
            (conv.client_type === ClientType.TRADER && conv.trader) ||
            (conv.client_type === ClientType.PLUMBER && conv.plumber)
    );

    // Count by original status
    const breakdown = {
        pending_to_active: 0,
        inactive_to_active: 0,
        dormant_to_active: 0,
    };

    inspectorConversions.forEach(conv => {
        const oldStatus = conv.old_status?.toUpperCase();
        if (oldStatus === 'PENDING') breakdown.pending_to_active++;
        else if (oldStatus === 'INACTIVE') breakdown.inactive_to_active++;
        else if (oldStatus === 'DORMANT') breakdown.dormant_to_active++;
    });

    const totalConversions = inspectorConversions.length;

    // Get total clients for conversion rate calculation
    const totalTraders = await Trader.count({ where: { inspector_id: inspectorId } });
    const totalPlumbers = await Plumber.count({ where: { inspector_id: inspectorId } });
    const totalClients = totalTraders + totalPlumbers;

    return {
        total_clients: totalClients,
        conversions: totalConversions,
        conversion_rate: totalClients > 0 ? Number(((totalConversions / totalClients) * 100).toFixed(2)) : 0,
        breakdown,
    };
};

/**
 * Calculate retention - clients who are ACTIVE during the period
 */
export const getRetentionStats = async (
    inspectorId: number,
    period: TimePeriod
) => {
    // Count ACTIVE traders
    const activeTraders = await Trader.count({
        where: {
            inspector_id: inspectorId,
            status: TraderActivityStatus.ACTIVE,
        },
    });

    // Count ACTIVE plumbers
    const activePlumbers = await Plumber.count({
        where: {
            inspector_id: inspectorId,
            status: PlumberAccountStatus.ACTIVE,
        },
    });

    const totalActive = activeTraders + activePlumbers;

    // Total clients for percentage
    const totalTraders = await Trader.count({ where: { inspector_id: inspectorId } });
    const totalPlumbers = await Plumber.count({ where: { inspector_id: inspectorId } });
    const totalClients = totalTraders + totalPlumbers;

    return {
        active_clients: totalActive,
        total_clients: totalClients,
        retention_rate: totalClients > 0 ? Number(((totalActive / totalClients) * 100).toFixed(2)) : 0,
    };
};

/**
 * Calculate sales statistics
 */
export const getSalesStats = async (
    inspectorId: number,
    period: TimePeriod
) => {
    // Get all visit reports for this inspector's clients in the period
    const traderSales = await VisitReport.findAll({
        where: {
            createdAt: {
                [Op.between]: [period.start_date, period.end_date],
            },
            sales_value: {
                [Op.gt]: 0,
            },
        },
        include: [
            {
                model: Trader,
                as: 'trader',
                where: { inspector_id: inspectorId },
                required: true,
            },
        ],
    });

    const plumberSales = await VisitReport.findAll({
        where: {
            createdAt: {
                [Op.between]: [period.start_date, period.end_date],
            },
            sales_value: {
                [Op.gt]: 0,
            },
        },
        include: [
            {
                model: Plumber,
                as: 'plumber',
                where: { inspector_id: inspectorId },
                required: true,
            },
        ],
    });

    const allSales = [...traderSales, ...plumberSales];

    // Calculate totals
    let totalAmount = 0;
    let directAmount = 0;
    let indirectAmount = 0;
    let directCount = 0;
    let indirectCount = 0;

    allSales.forEach(sale => {
        const amount = Number(sale.sales_value || 0);
        totalAmount += amount;

        if (sale.sales_classification === 'direct_sales') {
            directAmount += amount;
            directCount++;
        } else if (sale.sales_classification === 'indirect_sales') {
            indirectAmount += amount;
            indirectCount++;
        }
    });

    return {
        total: {
            amount: Number(totalAmount.toFixed(2)),
            count: allSales.length,
            average: allSales.length > 0 ? Number((totalAmount / allSales.length).toFixed(2)) : 0,
        },
        direct: {
            amount: Number(directAmount.toFixed(2)),
            count: directCount,
            percentage: totalAmount > 0 ? Number(((directAmount / totalAmount) * 100).toFixed(2)) : 0,
        },
        indirect: {
            amount: Number(indirectAmount.toFixed(2)),
            count: indirectCount,
            percentage: totalAmount > 0 ? Number(((indirectAmount / totalAmount) * 100).toFixed(2)) : 0,
        },
    };
};

/**
 * Calculate overview statistics - total counts
 */
export const getOverviewStats = async (
    inspectorId: number,
    period: TimePeriod
) => {
    const where = { inspector_id: inspectorId };

    const [
        activeTraders, inactiveTraders, dormantTraders, pendingTraders,
        activePlumbers, inactivePlumbers, dormantPlumbers, pendingPlumbers,
        totalVisits, approvedVisits, envoySetting
    ] = await Promise.all([
        Trader.count({ where: { ...where, status: TraderActivityStatus.ACTIVE } }),
        Trader.count({ where: { ...where, status: TraderActivityStatus.INACTIVE } }),
        Trader.count({ where: { ...where, status: TraderActivityStatus.DORMANT } }),
        Trader.count({ where: { ...where, status: TraderActivityStatus.PENDING } }),
        Plumber.count({ where: { ...where, status: PlumberAccountStatus.ACTIVE } }),
        Plumber.count({ where: { ...where, status: PlumberAccountStatus.INACTIVE } }),
        Plumber.count({ where: { ...where, status: PlumberAccountStatus.DORMANT } }),
        Plumber.count({ where: { ...where, status: PlumberAccountStatus.PENDING } }),
        InspectionVisit.count({
            where: {
                inspector_id: inspectorId,
                createdAt: {
                    [Op.between]: [period.start_date, period.end_date],
                },
            },
        }),
        InspectionVisit.count({
            where: {
                inspector_id: inspectorId,
                status: VisitStatus.APPROVED,
                createdAt: {
                    [Op.between]: [period.start_date, period.end_date],
                },
            },
        }),
        EnvoySetting.findOne({ where: { user_id: inspectorId } }),
    ]);

    return {
        total_clients: activeTraders + inactiveTraders + dormantTraders + pendingTraders +
            activePlumbers + inactivePlumbers + dormantPlumbers + pendingPlumbers,
        active_count: activeTraders + activePlumbers,
        inactive_count: inactiveTraders + inactivePlumbers,
        dormant_count: dormantTraders + dormantPlumbers,
        pending_count: pendingTraders + pendingPlumbers,
        total_visits: totalVisits,
        approved_visits: approvedVisits,
        target: envoySetting?.target || 0,
    };
};

/**
 * Get complete envoy statistics
 */
export const getEnvoyStatistics = async (
    inspectorId: number,
    periodType: TimePeriodType,
    date?: Date
) => {
    const period = getTimePeriod(periodType, date);

    const [conversion, retention, sales, overview] = await Promise.all([
        getConversionStats(inspectorId, period),
        getRetentionStats(inspectorId, period),
        getSalesStats(inspectorId, period),
        getOverviewStats(inspectorId, period),
    ]);

    return {
        period: {
            type: period.type,
            start_date: period.start_date.toISOString().split('T')[0],
            end_date: period.end_date.toISOString().split('T')[0],
        },
        overview,
        conversion,
        retention,
        sales,
    };
};
