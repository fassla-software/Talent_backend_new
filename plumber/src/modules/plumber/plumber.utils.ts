import { Sequelize } from 'sequelize';
import User from '../user/user.model';
import Plumber from './plumber.model';
import InspectionRequest, { RequestStatus } from '../inspectionRequest/inspection_request.model';
import { Op } from 'sequelize';
import InspectionRequestItem from '../inspectionRequest/inspection_request-items.model';
import PlumberCategory from '../plumberCategory/plumber-category.model';
import ReceivedGift from '../gift/receivedGift/received_gift.model';
import Gift from '../gift/gift.model';

export const getPlumberCategories = async () => {
  return await PlumberCategory.findAll({
    attributes: ['id', 'name', 'parent_id'],
    raw: true,
  });
};

export const getTheTopParent = (allCategories: PlumberCategory[], categoryId: number) => {
  let currentCategory = allCategories.find(category => category.id === categoryId);
  while (currentCategory && currentCategory.parent_id !== null) {
    currentCategory = allCategories.find(category => category.id === currentCategory?.parent_id);
  }
  return { topCategoryId: currentCategory?.id, topCategoryName: currentCategory?.name };
};

export const formatPlumberRequests = async (requests: InspectionRequest[]) => {
  const allCategories = await getPlumberCategories();
  return requests.map(request => {
    console.log({ request });
    const requestItems = request.items
      ?.flatMap(item => {
        const { subcategory } = item;
        if (!subcategory) {
          return null;
        }
        const parentCategory = getTheTopParent(allCategories, subcategory.id);
        return {
          category_id: subcategory.id,
          name: subcategory.name,
          category_count: item.count,
          category_points: subcategory.points,
          ...parentCategory,
        };
      })
      .filter(item => item !== null);

    // Sum category points for the current request
    const sumMap = new Map();
    requestItems?.forEach(item => {
      if (!item) return;
      const topCategoryId = `${item?.topCategoryId}`;
      const categorySum = (item?.category_points || 0) * (item?.category_count || 0); // Multiplied points by count
      const currentSum = sumMap.get(topCategoryId) || 0;
      sumMap.set(topCategoryId, currentSum + categorySum);
    });
    console.log(Object.entries(sumMap));

    const categorySum = Array.from(sumMap.entries()).map(([topCategoryId, totalSum]) => {
      const category = allCategories.find(cat => cat.id === Number(topCategoryId));
      return {
        category_name: category?.name || 'Unknown',
        category_sum: totalSum,
      };
    });

    return {
      request_id: request.id,
      ...request,
      categorySum,
    };
  });
};

export const getPlumberDetails = async (id: string): Promise<Plumber | null> => {
  return await Plumber.findOne({
    where: {
      user_id: id,
    },
    attributes: ['user_id', 'instant_withdrawal', 'fixed_points', 'gift_points'],
    include: [
      {
        model: User,
        attributes: [
          'name',
          'phone',
         'refer_code',
          [
            Sequelize.literal(
              `(SELECT COUNT(*) FROM \`inspection_requests\` 
                    WHERE \`inspection_requests\`.\`requestor_id\` = \`user\`.\`id\` 
                    AND \`inspection_requests\`.\`status\` = 'APPROVED')`,
            ),
            'approved_requests_count',
          ],
          [
            Sequelize.literal(
              `(SELECT COUNT(*) FROM \`inspection_requests\` 
                    WHERE \`inspection_requests\`.\`requestor_id\` = \`user\`.\`id\` 
                    AND \`inspection_requests\`.\`status\` = 'CANCELLED')`,
            ),
            'canceled_requests_count',
          ],
        ],
        as: 'user',
        include: [
          {
            model: InspectionRequest,
            as: 'requests',
            where: {
              requestor_id: id,
              status: { [Op.in]: [RequestStatus.APPROVED] },
            },
            attributes: ['id', 'city', 'area', 'status', 'inspection_date'],
            required: false,
            include: [
              {
                model: User,
                as: 'inspector',
                required: false,
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
          },
          {
            model: ReceivedGift,
            as: 'received_gifts',
            where: {
              user_id: id,
            },
            include: [
              {
                model: Gift,
                as: 'gift',
                attributes: ['name', 'points_required'],
              },
            ],
            required: false,
          },
        ],
      },
    ],
  });
};

export const formatPlumberReportRequests = async (requests: InspectionRequest[]) => {
  const allCategories = await getPlumberCategories();
  return requests.map(request => {
    const requestItems = request.items
      ?.flatMap(item => {
        const { subcategory } = item;
        if (!subcategory) {
          return null;
        }
        const parentCategory = getTheTopParent(allCategories, subcategory.id);
        return {
          category_id: subcategory.id,
          category_name: subcategory.name,
          category_count: item.count,
          category_points: subcategory.points,
          ...parentCategory,
        };
      })
      .filter(item => item !== null);
    // Sum category points for the current request
    const sumMap = new Map();
    requestItems?.forEach(item => {
      if (!item) return;
      const topCategoryId = `${item?.topCategoryId}`;
      const categorySum = item?.category_points || 0;
      // * (item?.category_count || 0); // Multiplied points by count

      const currentSum = sumMap.get(topCategoryId) || 0;
      sumMap.set(topCategoryId, currentSum + categorySum);
    });

    const categorySum = Array.from(sumMap.entries()).map(([topCategoryId, totalSum]) => {
      const category = allCategories.find(cat => cat.id === Number(topCategoryId));
      return {
        category_name: category?.name || 'Unknown',
        category_sum: totalSum,
      };
    });

    return {
      ...request,
      items: requestItems,
      categorySum,
    };
  });
};

export const getPlumberReportDetails = async (id: string): Promise<Plumber | null> => {
  return await Plumber.findOne({
    where: {
      user_id: id,
    },
    attributes: ['user_id'],
    include: [
      {
        model: User,
        attributes: ['name', 'phone'],
        as: 'user', // plumber
        include: [
          {
            model: InspectionRequest,
            as: 'requests',
            where: {
              requestor_id: id,
              status: { [Op.in]: [RequestStatus.APPROVED] },
            },
            attributes: ['id', 'city', 'area', 'status', 'inspection_date'],
            required: false,
            include: [
              {
                model: User,
                as: 'inspector',
                required: false,
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
          },
        ],
      },
    ],
  });
};
