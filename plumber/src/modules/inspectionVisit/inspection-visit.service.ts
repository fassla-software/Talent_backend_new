import { Op } from 'sequelize';
import HttpError from '../../utils/HttpError';
import InspectionVisit from './inspection-visit.model';
import VisitReport from './visit-report.model';
import User from '../user/user.model';
import Trader, { TraderActivityStatus } from '../trader/trader.model';
import Plumber, { PlumberAccountStatus } from '../plumber/plumber.model';
import { saveImages, viewImages } from '../../utils/imageUtils';
import InspectionRequest, { RequestStatus } from '../inspectionRequest/inspection_request.model';
import InspectionRequestItem from '../inspectionRequest/inspection_request-items.model';

export interface ICheckInData {
  latitude: number;
  longitude: number;
  trader_id?: number;
  plumber_id?: number;
}

export interface ICheckOutData {
  inspection_visit_id: number;
  latitude: number;
  longitude: number;
}

export interface ISubmitVisitReportData {
  inspection_visit_id: number;
  // Customer Information
  customer_name: string;
  company_name?: string;
  location: string;
  region_province?: string;
  phone: string;
  email?: string;
  client_type?: string;
  visit_type?: string;
  // Visit Details
  visit_result: string;
  interest_level?: string;
  purchase_readiness?: string;
  authority_level?: string;
  sales_value?: number;
  planned_purchase_date?: string;
  outcome_classification?: string;
  next_action?: string;
  // Sales Classification
  sales_classification?: string;
  // Additional
  notes?: string;
  images?: string[];
}

/**
 * Check-in for an inspection visit
 */
export const checkIn = async (inspectorId: number, data: ICheckInData) => {
  const { latitude, longitude, trader_id, plumber_id } = data;

  // Check if already checked in
  const existingVisit = await InspectionVisit.findOne({
    where: {
      ...(trader_id ? { trader_id } : {}),
      ...(plumber_id ? { plumber_id } : {}),
      inspector_id: inspectorId,
    },
  });

  if (existingVisit && existingVisit.isCheckedIn()) {
    throw new HttpError('Already checked in for this inspection', 422);
  }

  if (existingVisit) {
    // Update existing record
    await existingVisit.update({
      check_in_at: new Date(),
      check_in_latitude: latitude,
      check_in_longitude: longitude,
      trader_id: trader_id || null,
      plumber_id: plumber_id || null,
    });
    return existingVisit;
  } else {
    // Create new check-in record
    const visit = await InspectionVisit.create({
      inspector_id: inspectorId,
      trader_id: trader_id || null,
      plumber_id: plumber_id || null,
      check_in_at: new Date(),
      check_in_latitude: latitude,
      check_in_longitude: longitude,
    });
    return visit;
  }
};

/**
 * Submit visit report form before check-out
 */
export const submitVisitReport = async (inspectorId: number, data: ISubmitVisitReportData) => {
  const { images, inspection_visit_id, ...visitData } = data;

  // Find visit by inspection_visit_id
  const visit = await InspectionVisit.findByPk(inspection_visit_id);

  if (!visit) {
    throw new HttpError('Inspection visit not found', 404);
  }

  // Verify the visit belongs to the inspector
  if (visit.inspector_id !== inspectorId) {
    throw new HttpError('This inspection visit does not belong to you', 403);
  }

  if (!visit.isCheckedIn()) {
    throw new HttpError('Please check in first before submitting the visit report', 422);
  }

  if (visit.isCheckedOut()) {
    throw new HttpError('Already checked out. Cannot update visit report', 422);
  }

  // Get trader_id or plumber_id from the visit
  const traderId = visit.trader_id;
  const plumberId = visit.plumber_id;

  // Update trader status if sales_value is present
  if (visitData.sales_value && traderId) {
    const trader = await Trader.findByPk(traderId);
    if (trader && trader.status !== TraderActivityStatus.ACTIVE) {
      await trader.update({ status: TraderActivityStatus.ACTIVE });
    }
  }

  // Update plumber status if sales_value is present
  if (visitData.sales_value && plumberId) {
    const plumber = await Plumber.findByPk(plumberId);
    if (plumber && plumber.status !== PlumberAccountStatus.ACTIVE) {
      await plumber.update({ status: PlumberAccountStatus.ACTIVE });
    }
  }

  // Save images if provided
  let savedImages: string[] = [];
  if (images && images.length > 0) {
    savedImages = saveImages(images) as string[];
  }

  // Create or update visit report
  const visitReport = await VisitReport.upsert(
    {
      id: visit.report_id || undefined,
      trader_id: traderId || undefined,
      plumber_id: plumberId || undefined,
      customer_name: visitData.customer_name,
      company_name: visitData.company_name,
      location: visitData.location,
      region_province: visitData.region_province,
      phone: visitData.phone,
      email: visitData.email,
      client_type: visitData.client_type,
      visit_type: visitData.visit_type,
      visit_result: visitData.visit_result,
      interest_level: visitData.interest_level,
      purchase_readiness: visitData.purchase_readiness,
      authority_level: visitData.authority_level,
      sales_value: visitData.sales_value,
      planned_purchase_date: visitData.planned_purchase_date ? new Date(visitData.planned_purchase_date) : null,
      outcome_classification: visitData.outcome_classification,
      next_action: visitData.next_action,
      sales_classification: visitData.sales_classification,
      additional_notes: visitData.notes,
      photos: savedImages.length > 0 ? savedImages : null,
    },
    {
      returning: true,
    },
  );

  const report = visitReport[0];

  // Update inspection visit with report_id
  await visit.update({
    report_id: report.id,
  });

  // If next_action is provided, create a new inspection request for the inspector
  if (visitData.next_action && visitData.next_action.trim() !== '') {
    // Parse location to extract city and area if possible
    const locationParts = visitData.location.split(',').map(part => part.trim());
    const city = visitData.region_province || locationParts[locationParts.length - 1] || 'غير محدد';
    const area = locationParts.length > 1 ? locationParts[0] : 'غير محدد';

    // Get user_id from plumber if plumberId exists
    let requestorUserId: number | null = null;
    if (plumberId) {
      const plumber = await Plumber.findByPk(plumberId);
      if (plumber && plumber.user_id) {
        requestorUserId = plumber.user_id;
      }
    }

    // Create inspection request from visit report data
    const inspectionRequest = await InspectionRequest.create({
      requestor_id: requestorUserId,
      inspector_id: inspectorId,
      user_name: visitData.customer_name,
      user_phone: visitData.phone,
      nationality_id: '',
      area: area,
      city: city,
      address: visitData.location,
      seller_name: visitData.company_name || visitData.customer_name,
      seller_phone: visitData.phone,
      certificate_id: '',
      inspection_date: new Date(),
      description: visitData.next_action,
      images: savedImages.length > 0 ? savedImages : [],
      status: RequestStatus.ASSIGNED,
      user_lat: visit.check_in_latitude ? Number(visit.check_in_latitude) : 0,
      user_long: visit.check_in_longitude ? Number(visit.check_in_longitude) : 0,
    });

    // Note: items are optional for requests created from visit reports
    // If needed, they can be added later
  }

  return {
    visit_id: visit.id,
    report_id: report.id,
    has_report: true,
  };
};

/**
 * Check-out for an inspection visit
 */
export const checkOut = async (inspectorId: number, data: ICheckOutData) => {
  const { latitude, longitude, inspection_visit_id } = data;

  // Find visit by inspection_visit_id
  const visit = await InspectionVisit.findByPk(inspection_visit_id);

  if (!visit) {
    throw new HttpError('Inspection visit not found', 404);
  }

  // Verify the visit belongs to the inspector
  if (visit.inspector_id !== inspectorId) {
    throw new HttpError('This inspection visit does not belong to you', 403);
  }

  if (!visit.isCheckedIn()) {
    throw new HttpError('Please check in first before checking out', 422);
  }

  if (visit.isCheckedOut()) {
    throw new HttpError('Already checked out for this inspection', 422);
  }

  // Check if visit report is filled (required before check-out)
  if (!visit.hasVisitReport()) {
    throw new HttpError('Please fill out the visit report form before checking out', 422);
  }

  await visit.update({
    check_out_at: new Date(),
    check_out_latitude: latitude,
    check_out_longitude: longitude,
  });

  return visit;
};

/**
 * Get visit status for an inspection visit
 */
export const getVisitStatus = async (inspectorId: number, traderId?: number, plumberId?: number) => {
  const visit = await InspectionVisit.findOne({
    where: {
      ...(traderId ? { trader_id: traderId } : {}),
      ...(plumberId ? { plumber_id: plumberId } : {}),
      inspector_id: inspectorId,
    },
    include: [
      {
        model: VisitReport,
        as: 'visitReport',
      },
    ],
  });

  if (!visit) {
    return {
      status: 'not_checked_in',
      is_checked_in: false,
      is_checked_out: false,
      has_report: false,
    };
  }

  const visitObject = visit.toJSON();
  if (visitObject.visitReport && visitObject.visitReport.photos) {
    visitObject.visitReport.photos = viewImages(visitObject.visitReport.photos);
  }

  return {
    status: visit.isCheckedOut() ? 'checked_out' : visit.isCheckedIn() ? 'checked_in' : 'not_checked_in',
    is_checked_in: visit.isCheckedIn(),
    is_checked_out: visit.isCheckedOut(),
    has_report: visit.hasVisitReport(),
    check_in: visit.isCheckedIn()
      ? {
        check_in_at: visit.check_in_at,
        latitude: visit.check_in_latitude,
        longitude: visit.check_in_longitude,
      }
      : null,
    check_out: visit.isCheckedOut()
      ? {
        check_out_at: visit.check_out_at,
        latitude: visit.check_out_latitude,
        longitude: visit.check_out_longitude,
      }
      : null,
    visit_report: visitObject.visitReport,
  };
};


/**
 * Get all visits for envoy (inspector)
 */
export const getEnvoyVisits = async (inspectorId: number) => {
  const visits = await InspectionVisit.findAll({
    where: {
      inspector_id: inspectorId,
    },
    include: [
      {
        model: Trader,
        as: 'trader',
        attributes: ['id', 'city', 'area'],
        required: false,
      },
      {
        model: Plumber,
        as: 'plumber',
        attributes: ['id', 'city', 'area'],
        required: false,
      },
      {
        model: VisitReport,
        as: 'visitReport',
        attributes: ['visit_result', 'company_name'],
      },
    ],
    order: [['createdAt', 'DESC']],
  });

  return visits.map(visit => ({
    id: visit.id,
    trader_id: visit.trader_id,
    plumber_id: visit.plumber_id,
    trader_city: visit.trader?.city,
    trader_area: visit.trader?.area,
    plumber_city: visit.plumber?.city,
    plumber_area: visit.plumber?.area,
    company_name: visit.visitReport?.company_name || null,
    visit_result: visit.visitReport?.visit_result || null,
    status: visit.status,
  }));
};

/**
 * Get all visits for admin with pagination
 */
export const getAdminVisits = async (page: number = 1, limit: number = 20) => {
  const offset = (page - 1) * limit;

  const { count, rows: visits } = await InspectionVisit.findAndCountAll({
    include: [
      {
        model: Trader,
        as: 'trader',
        attributes: ['id', 'city', 'area'],
        required: false,
      },
      {
        model: Plumber,
        as: 'plumber',
        attributes: ['id', 'city', 'area'],
        required: false,
      },
      {
        model: User,
        as: 'inspector',
        attributes: ['name', 'phone'],
        required: false,
      },
      {
        model: VisitReport,
        as: 'visitReport',
        attributes: ['visit_result', 'company_name'],
        required: false,
      },
    ],
    order: [['createdAt', 'DESC']],
    limit,
    offset,
  });

  return {
    visits: visits.map(visit => ({
      id: visit.id,
      trader_id: visit.trader_id,
      plumber_id: visit.plumber_id,
      trader_city: visit.trader?.city,
      trader_area: visit.trader?.area,
      plumber_city: visit.plumber?.city,
      plumber_area: visit.plumber?.area,
      company_name: visit.visitReport?.company_name || null,
      inspector_name: visit.inspector?.name,
      inspector_phone: visit.inspector?.phone,
      visit_result: visit.visitReport?.visit_result || null,
      status: visit.status,
      check_in_at: visit.check_in_at,
      check_out_at: visit.check_out_at,
      date: visit.createdAt,
    })),
    pagination: {
      total: count,
      page,
      limit,
      totalPages: Math.ceil(count / limit),
    },
  };
};

/**
 * Get visit details for admin
 */
export const getAdminVisitDetails = async (visitId: number) => {
  const visit = await InspectionVisit.findByPk(visitId, {
    include: [
      {
        model: Trader,
        as: 'trader',
        required: false,
      },
      {
        model: Plumber,
        as: 'plumber',
        required: false,
      },
      {
        model: User,
        as: 'inspector',
        attributes: ['name', 'phone', 'email'],
        required: false,
      },
      {
        model: VisitReport,
        as: 'visitReport',
        required: false,
      },
    ],
  });

  if (!visit) {
    throw new HttpError('Visit not found', 404);
  }

  const visitData = visit.toJSON() as any;
  if (visitData.visitReport?.photos) {
    visitData.visitReport.photos = viewImages(visitData.visitReport.photos);
  }

  return visitData;
};

/**
 * Update visit status (admin only)
 */
export const updateVisitStatus = async (visitId: number, status: string) => {
  const visit = await InspectionVisit.findByPk(visitId);

  if (!visit) {
    throw new HttpError('Visit not found', 404);
  }

  await visit.update({ status });
  return visit;
};

/**
 * Get weekly statistics for envoy
 * Week runs from Saturday to Friday
 */
export const getEnvoyWeeklyStatistics = async (inspectorId: number) => {
  const now = new Date();

  // Helper function to get Saturday of a given week
  const getSaturday = (date: Date, offset: number = 0): Date => {
    const day = date.getDay(); // 0 = Sunday, 6 = Saturday
    const diff = day === 6 ? offset * 7 : (day + 1 + offset * 7);
    const saturday = new Date(date);
    saturday.setDate(date.getDate() - diff);
    saturday.setHours(0, 0, 0, 0);
    return saturday;
  };

  // Get this week's Saturday (start of current week)
  const thisWeekStart = getSaturday(now, 0);
  const thisWeekEnd = new Date(thisWeekStart);
  thisWeekEnd.setDate(thisWeekStart.getDate() + 6); // Friday
  thisWeekEnd.setHours(23, 59, 59, 999);

  // Get last week's Saturday (start of previous week)
  const lastWeekStart = new Date(thisWeekStart);
  lastWeekStart.setDate(thisWeekStart.getDate() - 7);
  const lastWeekEnd = new Date(lastWeekStart);
  lastWeekEnd.setDate(lastWeekStart.getDate() + 6); // Friday
  lastWeekEnd.setHours(23, 59, 59, 999);

  // Count visits for this week (completed visits only - those with check_out_at)
  const thisWeekVisitsCount = await InspectionVisit.count({
    where: {
      inspector_id: inspectorId,
      check_out_at: {
        [Op.between]: [thisWeekStart, thisWeekEnd],
      },
    },
  });

  // Count visits for last week
  const lastWeekVisitsCount = await InspectionVisit.count({
    where: {
      inspector_id: inspectorId,
      check_out_at: {
        [Op.between]: [lastWeekStart, lastWeekEnd],
      },
    },
  });

  // Calculate progress
  let progressPercentage = 0;
  let trend: 'up' | 'down' | 'stable' = 'stable';
  const difference = thisWeekVisitsCount - lastWeekVisitsCount;

  if (lastWeekVisitsCount > 0) {
    progressPercentage = Number((((thisWeekVisitsCount - lastWeekVisitsCount) / lastWeekVisitsCount) * 100).toFixed(2));
  } else if (thisWeekVisitsCount > 0) {
    // If last week was 0, show 100% increase if there are visits this week
    progressPercentage = 100;
  }

  if (difference > 0) {
    trend = 'up';
  } else if (difference < 0) {
    trend = 'down';
  }

  // Count active traders
  const activeTraders = await Trader.count({
    where: {
      inspector_id: inspectorId,
      status: TraderActivityStatus.ACTIVE,
    },
  });

  // Count active plumbers
  const activePlumbers = await Plumber.count({
    where: {
      inspector_id: inspectorId,
      status: PlumberAccountStatus.ACTIVE,
    },
  });

  // Format dates for response
  const formatDate = (date: Date): string => {
    return date.toISOString().split('T')[0];
  };

  return {
    this_week: {
      visits_count: thisWeekVisitsCount,
      start_date: formatDate(thisWeekStart),
      end_date: formatDate(thisWeekEnd),
    },
    last_week: {
      visits_count: lastWeekVisitsCount,
      start_date: formatDate(lastWeekStart),
      end_date: formatDate(lastWeekEnd),
    },
    progress: {
      percentage: progressPercentage,
      difference,
      trend,
    },
    active_clients: {
      traders: activeTraders,
      plumbers: activePlumbers,
      total: activeTraders + activePlumbers,
    },
  };
};

