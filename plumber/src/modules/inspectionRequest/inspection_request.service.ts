import path from 'path';
import HttpError from '../../utils/HttpError';
import { saveImages, viewImages } from '../../utils/imageUtils';
import Certificate from '../certificate/certificate.model';
import PlumberCategory from '../plumberCategory/plumber-category.model';
import { Roles } from '../role/role.model';
import { ICreateInspectionRequest } from './dto/create-inspection_request.dto';
import { IFilter } from './dto/filer.dto';
import InspectionRequestItem from './inspection_request-items.model';
import InspectionRequest, { RequestStatus } from './inspection_request.model';
import { getConfig } from 'dotenv-handler';
import { createCertificatePDF } from '../certificate/certificate.service';
import { calcUserPoints, getAllParents, haversineDistance } from './inspect-request.utils';
import Plumber from '../plumber/plumber.model';
import { Sequelize } from 'sequelize';
import { Op } from 'sequelize';
import User from '../user/user.model';
import { getPlumberCategories } from '../plumber/plumber.utils';
import { getConfigService } from '../config/config.service';
import { RequestFilterStatus } from './dto/request-filter-status.dto';
import { sendPushNotification } from '../../utils/notification';

const BASE_URL = getConfig('BASE_URL');
export const addInspectionRequest = async (
  requestor_id: string,
  requestor_role: string,
  data: ICreateInspectionRequest,
) => {
  const {
    user_name,
    inspection_date,
    user_phone,
    nationality_id,
    city,
    area,
    address,
    seller_name,
    seller_phone,
    items,
    certificate_id,
    description,
    images,
    status,
    user_lat,
    user_long,
    plumber_id,
  } = data;
  console.log(data);

  const isEnvoy = requestor_role === Roles.Envoy;

  if (isEnvoy && !plumber_id) {
    throw new HttpError('plumber_id is required for envoy requests', 400);
  }

  const inspectionDate = new Date(
    Date.UTC(
      new Date(inspection_date).getFullYear(),
      new Date(inspection_date).getMonth(),
      new Date(inspection_date).getDate(),
    ),
  );

  const imagesName = saveImages(images) as string[];
  const request = await InspectionRequest.create({
    requestor_id: isEnvoy ? plumber_id : requestor_id,
    ...(isEnvoy ? { inspector_id: requestor_id } : {}),
    user_name,
    user_phone,
    nationality_id,
    area,
    city,
    address,
    seller_name,
    seller_phone,
    certificate_id,
    inspection_date: inspectionDate,
    description,
    images: [...imagesName],
    status: isEnvoy ? RequestStatus.ASSIGNED : (status ?? RequestStatus.PENDING),
    user_lat,
    user_long,
  });

  const itemsData = items.map(item => ({
    inspection_request_id: request.id,
    subcategory_id: item.subcategory_id,
    count: item.count,
  }));

  await InspectionRequestItem.bulkCreate(itemsData);

  return request;
};

export const getInspectionRequest = async (id: string) => {
  const requestWithItems = await InspectionRequest.findOne({
    where: { id: id },
    include: [
      {
        model: InspectionRequestItem,
        as: 'items',
        include: [
          {
            model: PlumberCategory,
            as: 'subcategory',
          },
        ],
      },
    ],
    order: [['createdAt', 'DESC']],
  });

  if (!requestWithItems) {
    throw new HttpError('request not found', 404);
  }

  const requestObject = requestWithItems.toJSON();
  requestObject.images = requestObject.images ? viewImages(JSON.parse(requestObject.images) as string[]) : [];
  requestObject.inspection_images = requestObject.inspection_images
    ? viewImages(JSON.parse(requestObject.inspection_images) as string[])
    : [];

  if (requestObject.items) {
    requestObject.items.forEach((item: { subcategory: { image: string } }) => {
      item.subcategory.image = item.subcategory.image ? (viewImages(item.subcategory.image) as string) : '';
    });
  }
  if (
    requestObject.user_lat &&
    requestObject.user_long &&
    requestObject.inspection_lat &&
    requestObject.inspection_long
  ) {
    requestObject.distance = haversineDistance(
      requestObject.user_lat,
      requestObject.user_long,
      requestObject.inspection_lat,
      requestObject.inspection_long,
    );
  } else {
    // If any lat or long is missing, set distance to null
    requestObject.distance = null;
  }
  return requestObject;
};

export const getInspectionRequests = async (filter: IFilter) => {
  const { status, limit = 10, skip = 0, requestor_id, inspector_id } = filter;
  console.log({ filter });

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const conditions: any = {};
  if (status) {
    conditions.status = status;
  }
  if (requestor_id) {
    conditions.requestor_id = requestor_id;
  }
  if (inspector_id) {
    conditions.inspector_id = inspector_id;
  }
  const requestsWithItems = await InspectionRequest.findAll({
    where: conditions,
    include: [
      {
        model: User,
        as: 'requestor',
        attributes: ['name', 'phone'],
      },
      {
        model: InspectionRequestItem,
        as: 'items',
        include: [
          {
            model: PlumberCategory,
            as: 'subcategory',
          },
        ],
      },
    ],
    limit: Number(limit),
    offset: Number(skip),
    order: [['createdAt', 'DESC']],
  });
  // Map through each request and convert to plain object
  const requestObjects = requestsWithItems.map(request => {
    const requestObject = request.toJSON();
    requestObject.images = requestObject.images ? viewImages(JSON.parse(requestObject.images) as string[]) : [];
    requestObject.inspection_images = requestObject.inspection_images
      ? viewImages(JSON.parse(requestObject.inspection_images) as string[])
      : [];
    if (
      requestObject.user_lat &&
      requestObject.user_long &&
      requestObject.inspection_lat &&
      requestObject.inspection_long
    ) {
      requestObject.distance = haversineDistance(
        requestObject.user_lat,
        requestObject.user_long,
        requestObject.inspection_lat,
        requestObject.inspection_long,
      );
    } else {
      requestObject.distance = null;
    }
    // Remove latitudes and longitudes from the response
    delete requestObject.user_lat;
    delete requestObject.user_long;
    delete requestObject.inspection_lat;
    delete requestObject.inspection_long;
    requestObject.items.forEach((item: { subcategory: { image: string } }) => {
      item.subcategory.image = item.subcategory.image ? (viewImages(item.subcategory.image) as string) : '';
    });
    return requestObject;
  });

  return requestObjects;
};

export const getUserRequests = async (id: string, role: string, filter: IFilter) => {
  const { area, city, plumber_name, status, user_name, limit = 10, skip = 0 } = filter;
  console.log({ filter });

  // For Envoy: get all tasks assigned to them (by admin) OR created by them (from visit reports)
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const conditions: any =
    role === Roles.Envoy
      ? {
        inspector_id: id, // All tasks assigned to this envoy
      }
      : {
        requestor_id: id, // For plumber: only tasks they created
      };

  if (status) {
    switch (status) {
      case RequestFilterStatus.ACCEPTED:
        conditions.status = {
          [Op.or]: [RequestStatus.ACCEPTED, RequestStatus.APPROVED],
        };
        break;

      case RequestFilterStatus.REJECTED:
        if (role === Roles.Envoy || role === Roles.PLUMBER) {
          conditions.status = {
            [Op.or]: [RequestStatus.REJECTED, RequestStatus.CANCELLED],
          };
        }
        break;

      case RequestFilterStatus.UNDER_REVIEW:
        if (role === Roles.Envoy) {
          conditions.status = RequestStatus.ASSIGNED; // Envoy gets only ASSIGNED requests
        } else if (role === Roles.PLUMBER) {
          conditions.status = RequestStatus.ASSIGNED; // Plumber gets only SEND requests
        }
        break;

      case RequestFilterStatus.SEND:
        if (role === Roles.PLUMBER) {
          conditions.status = RequestStatus.SEND; // Plumber gets SEND requests
        }
        break;
      case RequestFilterStatus.REVIEWED:
        if (role === Roles.Envoy) {
          conditions.status = {
            [Op.or]: [RequestStatus.APPROVED, RequestStatus.CANCELLED],
          };
        }
        break;
      case RequestFilterStatus.PENDING:
        if (role === Roles.Envoy) {
          conditions.status = RequestStatus.PENDING;
        }
        break;
      default:
        // If no status filter is provided, don't filter by status
        // This will return all requests for the user (both assigned by admin and created from next_action)
        break;
    }
  }

  if (area) {
    conditions.area = { [Op.like]: `%${area}%` };
  }
  if (city) {
    conditions.city = { [Op.like]: `%${city}%` };
  }
  if (user_name) {
    conditions.user_name = { [Op.like]: `%${user_name}%` };
  }
  console.log({ conditions });

  // Fetch requests with items and related data
  const requestsWithItems = await InspectionRequest.findAll({
    where: conditions,
    include: [
      {
        model: InspectionRequestItem,
        as: 'items',
        include: [
          {
            model: PlumberCategory,
            as: 'subcategory',
          },
        ],
      },
      {
        model: User,
        as: 'requestor',
        attributes: ['name', 'phone'],
        required: false, // Allow requests without requestor (e.g., from next_action)
        where: plumber_name ? { name: { [Op.like]: `%${plumber_name}%` } } : undefined,
      },
      {
        model: User,
        as: 'inspector',
        attributes: ['name', 'phone'],
        required: false,
      },
    ],
    limit: Number(limit),
    offset: Number(skip),
    order: [['createdAt', 'DESC']],
  });

  // Transform the fetched data
  const requestObjects = await Promise.all(
    requestsWithItems.map(async request => {
      const requestObject = request.toJSON();
      // If requestor is null (e.g., requests from next_action), use inspector data instead
      requestObject.plumber_name = requestObject.requestor?.name || requestObject.inspector?.name || null;
      requestObject.plumber_phone = requestObject.requestor?.phone || requestObject.inspector?.phone || null;

      // Add flag to identify requests from visit reports (for UI differentiation)
      requestObject.from_visit_report = !!requestObject.visit_report_id;

      delete requestObject.requestor;
      delete requestObject.inspector;

      // Parse and format images
      requestObject.images = requestObject.images ? viewImages(JSON.parse(requestObject.images)) : [];
      requestObject.inspection_images = requestObject.inspection_images
        ? viewImages(JSON.parse(requestObject.inspection_images))
        : [];

      // Calculate distance if coordinates are available
      if (
        requestObject.user_lat &&
        requestObject.user_long &&
        requestObject.inspection_lat &&
        requestObject.inspection_long
      ) {
        requestObject.distance = haversineDistance(
          requestObject.user_lat,
          requestObject.user_long,
          requestObject.inspection_lat,
          requestObject.inspection_long,
        );
      } else {
        requestObject.distance = null;
      }

      // Remove sensitive coordinates
      delete requestObject.user_lat;
      delete requestObject.user_long;
      delete requestObject.inspection_lat;
      delete requestObject.inspection_long;

      // Fetch and attach categories
      const categories = await getPlumberCategories();
      if (requestObject.items) {
        requestObject.items = requestObject.items.map((item: InspectionRequestItem) => {
          const subcategory = item.subcategory;
          const parentCategories = getAllParents(categories, subcategory.id);
          const parentNames = parentCategories
            .reverse()
            .map(parent => parent.name)
            .join(' > ');
          const fullName = `${parentNames} > ${subcategory.name}`;

          return {
            ...item,
            subcategory: {
              ...subcategory,
              name: fullName,
              image: subcategory.image ? viewImages(subcategory.image) : '',
            },
          };
        });
      }

      return requestObject;
    }),
  );

  return requestObjects;
};

export const assignInspectionRequest = async (data: { request_id: string; inspector_id: string }) => {
  const { inspector_id, request_id } = data;
  const request = await InspectionRequest.findByPk(request_id);

  if (!request) {
    throw new HttpError('request not found', 404);
  }
  await request.update({
    inspector_id,
    status: RequestStatus.ASSIGNED,
  });

  // Trigger notification
  const title = 'New Inspection Assigned';
  const body = `A new inspection request for ${request.user_name} has been assigned to you in ${request.city}.`;
  sendPushNotification(Number(inspector_id), title, body);

  return request;
};

export const checkInspectionRequest = async (data: {
  request_id: string;
  comment: string;
  description: string;
  inspection_images: string[];
  inspection_lat: number;
  inspection_long: number;
  items: { subcategory_id: string; count: number }[];
  request_status: RequestStatus.ACCEPTED | RequestStatus.REJECTED;
}) => {
  const {
    description,
    items,
    comment,
    request_id,
    request_status,
    inspection_images,
    inspection_lat,
    inspection_long,
  } = data;
  console.log({ data });
  const request = await InspectionRequest.findByPk(request_id);
  if (!request) {
    throw new HttpError('Request not found', 404);
  }

  if (items && items.length > 0) {
    for (const item of items) {
      const { subcategory_id, count } = item;

      const [existingItem] = await InspectionRequestItem.findOrCreate({
        where: { subcategory_id, inspection_request_id: request_id },
        defaults: { count },
      });

      if (existingItem) {
        await existingItem.update({ count });
      }
    }
  }

  const imagesName = saveImages(inspection_images) as string[];
  console.log({ imagesName });
  await request.update({
    comment,
    description,
    inspection_images: [...imagesName],
    inspection_lat,
    inspection_long,
    status: request_status,
  });

  // Return the updated request
  return request;
};

export const approveInspectionRequest = async (data: {
  request_id: string;
  request_status: RequestStatus.APPROVED | RequestStatus.CANCELLED;
}) => {
  const { request_id, request_status } = data;
  const request = await InspectionRequest.findByPk(request_id);
  if (!request) {
    throw new HttpError('Request not found', 404);
  }
  if (request.status === RequestStatus.APPROVED) {
    throw new HttpError('Request is already approved', 400);
  }
  if (request_status == RequestStatus.CANCELLED) {
    await request.update({
      status: request_status,
    });
    return request;
  }

  const items = await InspectionRequestItem.findAll({
    where: { inspection_request_id: request.id },
    include: {
      model: PlumberCategory,
      as: 'subcategory',
      attributes: ['id', 'name', 'points'],
    },
  });
  const points = calcUserPoints(items);
  console.log({ points });
  const pointValue = await getConfigService('withdraw_points');
  const money = points * Number(pointValue);
  const [plumber] = await Plumber.update(
    {
      fixed_points: Sequelize.literal(`fixed_points + ${points}`),
      gift_points: Sequelize.literal(`gift_points + ${points}`),
      instant_withdrawal: Sequelize.literal(`instant_withdrawal + ${points}`),
      withdraw_money: Sequelize.literal(`withdraw_money + ${money}`),
    },
    {
      where: { user_id: request.requestor_id },
    },
  );
  if (!plumber) {
    throw new HttpError('Plumber nor found', 404);
  }
  if (!request.certificate_id) {
    throw new HttpError('Certificate ID is required', 400);
  }
  const fileUrl = await createCertificatePDF({
    certificate_id: request.certificate_id,
    user_name: request.user_name,
    user_phone: request.user_phone,
    company_name: 'Talent',
    company_phone: '12345679',
    city: request.city,
    address: request.address,
    date: request.inspection_date.toISOString().split('T')[0],
    url: `${BASE_URL}/PDF/${request.user_name}_${request.certificate_id}`,
    description: request.description || '',
  });

  const fileName = path.basename(fileUrl);
  console.log({ request });
  const certificate = await Certificate.create({
    plumber_id: request.requestor_id,
    certificate_id: request.certificate_id,
    user_phone: request.user_phone || '',
    nationality_id: request.nationality_id || '',
    file_name: fileName,
  });
  console.log({ certificate });
  await request.update({
    status: request_status,
  });
  return { ...request.toJSON(), file_name: certificate.file_name };
};

export const bulkDeleteInspectionRequests = async (requestIds: string[]) => {
  if (!requestIds || requestIds.length === 0) {
    throw new HttpError('No request IDs provided', 400);
  }

  const deletedCount = await InspectionRequest.destroy({
    where: {
      id: {
        [Op.in]: requestIds,
      },
    },
  });

  return {
    message: 'Requests deleted successfully',
    count: deletedCount,
    deleted: deletedCount,
    deletedCount: deletedCount,
  };
};

export const pendingInspectionRequest = async (data: {
  request_id: string;
  inspector_id: string;
  note: string;
}) => {
  const { request_id, inspector_id, note } = data;
  const request = await InspectionRequest.findByPk(request_id);

  if (!request) {
    throw new HttpError('Request not found', 404);
  }

  // Verify that the inspector owns this task
  if (String(request.inspector_id) !== String(inspector_id)) {
    throw new HttpError('You are not authorized to update this request', 403);
  }

  await request.update({
    status: RequestStatus.PENDING,
    note: note,
  });

  return request;
};

export const getInspectorPendingRequests = async (inspector_id: string, filter: IFilter) => {
  const { area, city, user_name, limit = 10, skip = 0 } = filter;

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const conditions: any = {
    inspector_id: inspector_id,
    status: RequestStatus.PENDING,
  };

  if (area) {
    conditions.area = { [Op.like]: `%${area}%` };
  }
  if (city) {
    conditions.city = { [Op.like]: `%${city}%` };
  }
  if (user_name) {
    conditions.user_name = { [Op.like]: `%${user_name}%` };
  }

  // Fetch requests with items and related data
  const requestsWithItems = await InspectionRequest.findAll({
    where: conditions,
    include: [
      {
        model: InspectionRequestItem,
        as: 'items',
        include: [
          {
            model: PlumberCategory,
            as: 'subcategory',
          },
        ],
      },
      {
        model: User,
        as: 'requestor',
        attributes: ['name', 'phone'],
        required: false, // Allow requests without requestor (e.g., from next_action)
      },
      {
        model: User,
        as: 'inspector',
        attributes: ['name', 'phone'],
        required: false,
      },
    ],
    limit: Number(limit),
    offset: Number(skip),
    order: [['createdAt', 'DESC']],
  });

  // Transform the fetched data
  const requestObjects = await Promise.all(
    requestsWithItems.map(async request => {
      const requestObject = request.toJSON();
      // If requestor is null (e.g., requests from next_action), use inspector data instead
      requestObject.plumber_name = requestObject.requestor?.name || requestObject.inspector?.name || null;
      requestObject.plumber_phone = requestObject.requestor?.phone || requestObject.inspector?.phone || null;

      // Add flag to identify requests from visit reports (for UI differentiation)
      requestObject.from_visit_report = !!requestObject.visit_report_id;

      delete requestObject.requestor;
      delete requestObject.inspector;

      // Parse and format images
      requestObject.images = requestObject.images ? viewImages(JSON.parse(requestObject.images)) : [];
      requestObject.inspection_images = requestObject.inspection_images
        ? viewImages(JSON.parse(requestObject.inspection_images))
        : [];

      // Calculate distance if coordinates are available
      if (
        requestObject.user_lat &&
        requestObject.user_long &&
        requestObject.inspection_lat &&
        requestObject.inspection_long
      ) {
        requestObject.distance = haversineDistance(
          requestObject.user_lat,
          requestObject.user_long,
          requestObject.inspection_lat,
          requestObject.inspection_long,
        );
      } else {
        requestObject.distance = null;
      }

      // Remove sensitive coordinates
      delete requestObject.user_lat;
      delete requestObject.user_long;
      delete requestObject.inspection_lat;
      delete requestObject.inspection_long;

      // Fetch and attach categories
      const categories = await getPlumberCategories();
      if (requestObject.items) {
        requestObject.items = requestObject.items.map((item: InspectionRequestItem) => {
          const subcategory = item.subcategory;
          const parentCategories = getAllParents(categories, subcategory.id);
          const parentNames = parentCategories
            .reverse()
            .map(parent => parent.name)
            .join(' > ');
          const fullName = `${parentNames} > ${subcategory.name}`;

          return {
            ...item,
            subcategory: {
              ...subcategory,
              name: fullName,
              image: subcategory.image ? viewImages(subcategory.image) : '',
            },
          };
        });
      }

      return requestObject;
    }),
  );

  return requestObjects;
};

export const getInspectorOverdueRequests = async (inspector_id: string, filter: IFilter) => {
  const { area, city, user_name, limit = 10, skip = 0 } = filter;

  // Calculate the date 24 hours ago from now
  const now = new Date();
  const twentyFourHoursAgo = new Date(now.getTime() - 24 * 60 * 60 * 1000);

  // Tasks are overdue if:
  // 1. inspection_date is more than 24 hours ago
  // 2. Status is not completed (not ACCEPTED, REJECTED, APPROVED, CANCELLED)
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const conditions: any = {
    inspector_id: inspector_id,
    inspection_date: {
      [Op.lt]: twentyFourHoursAgo, // inspection_date is more than 24 hours ago
    },
    status: {
      [Op.notIn]: [
        RequestStatus.ACCEPTED,
        RequestStatus.REJECTED,
        RequestStatus.APPROVED,
        RequestStatus.CANCELLED,
      ], // Not completed yet
    },
  };

  if (area) {
    conditions.area = { [Op.like]: `%${area}%` };
  }
  if (city) {
    conditions.city = { [Op.like]: `%${city}%` };
  }
  if (user_name) {
    conditions.user_name = { [Op.like]: `%${user_name}%` };
  }

  // Fetch requests with items and related data
  const requestsWithItems = await InspectionRequest.findAll({
    where: conditions,
    include: [
      {
        model: InspectionRequestItem,
        as: 'items',
        include: [
          {
            model: PlumberCategory,
            as: 'subcategory',
          },
        ],
      },
      {
        model: User,
        as: 'requestor',
        attributes: ['name', 'phone'],
        required: false,
      },
      {
        model: User,
        as: 'inspector',
        attributes: ['name', 'phone'],
        required: false,
      },
    ],
    limit: Number(limit),
    offset: Number(skip),
    order: [['inspection_date', 'ASC']], // Order by oldest inspection_date first
  });

  // Transform the fetched data
  const requestObjects = await Promise.all(
    requestsWithItems.map(async request => {
      const requestObject = request.toJSON();
      // If requestor is null (e.g., requests from next_action), use inspector data instead
      requestObject.plumber_name = requestObject.requestor?.name || requestObject.inspector?.name || null;
      requestObject.plumber_phone = requestObject.requestor?.phone || requestObject.inspector?.phone || null;

      // Add flag to identify requests from visit reports
      requestObject.from_visit_report = !!requestObject.visit_report_id;

      // Calculate hours overdue
      const inspectionDate = new Date(requestObject.inspection_date);
      const hoursOverdue = Math.floor((now.getTime() - inspectionDate.getTime()) / (1000 * 60 * 60));
      requestObject.hours_overdue = hoursOverdue;

      delete requestObject.requestor;
      delete requestObject.inspector;

      // Parse and format images
      requestObject.images = requestObject.images ? viewImages(JSON.parse(requestObject.images)) : [];
      requestObject.inspection_images = requestObject.inspection_images
        ? viewImages(JSON.parse(requestObject.inspection_images))
        : [];

      // Calculate distance if coordinates are available
      if (
        requestObject.user_lat &&
        requestObject.user_long &&
        requestObject.inspection_lat &&
        requestObject.inspection_long
      ) {
        requestObject.distance = haversineDistance(
          requestObject.user_lat,
          requestObject.user_long,
          requestObject.inspection_lat,
          requestObject.inspection_long,
        );
      } else {
        requestObject.distance = null;
      }

      // Remove sensitive coordinates
      delete requestObject.user_lat;
      delete requestObject.user_long;
      delete requestObject.inspection_lat;
      delete requestObject.inspection_long;

      // Fetch and attach categories
      const categories = await getPlumberCategories();
      if (requestObject.items) {
        requestObject.items = requestObject.items.map((item: InspectionRequestItem) => {
          const subcategory = item.subcategory;
          const parentCategories = getAllParents(categories, subcategory.id);
          const parentNames = parentCategories
            .reverse()
            .map(parent => parent.name)
            .join(' > ');
          const fullName = `${parentNames} > ${subcategory.name}`;

          return {
            ...item,
            subcategory: {
              ...subcategory,
              name: fullName,
              image: subcategory.image ? viewImages(subcategory.image) : '',
            },
          };
        });
      }

      return requestObject;
    }),
  );

  return requestObjects;
};
