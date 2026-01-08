import { Op } from 'sequelize';
import HttpError from '../../utils/HttpError';
import PlumberCategory from './plumber-category.model';
import { saveImages, viewImages } from '../../utils/imageUtils';

export const getPlumberCategoryTreeDynamic = async () => {
  const allCategories = await PlumberCategory.findAll();
  const categories = allCategories.map(category => category.toJSON());

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const buildTree = (parentId: number | null): any[] =>
    categories
      .filter(category => category.parent_id === parentId)
      .map(category => {
        const subcategories = buildTree(category.id);
        const itemsFlag = subcategories.length > 0 && subcategories.every(sub => sub.subcategories.length === 0);
        return {
          ...category,
          image: viewImages(category.image),
          itemsFlag,
          subcategories,
        };
      });

  return buildTree(null);
};

export const getCategoriesWithSubcategory = async () => {
  const categories = await PlumberCategory.findAll({
    where: { parent_id: null },
    include: {
      model: PlumberCategory,
      as: 'subcategories',
    },
  });

  const categoriesWithFlags = await Promise.all(
    categories.map(async category => {
      const subcategoriesWithFlags = await Promise.all(
        category.subcategories?.map(async item => {
          const exist = await PlumberCategory.findOne({ where: { parent_id: item.id } });
          return {
            ...item.toJSON(),
            has_subcategory: !!exist,
            image: item.image ? viewImages(item.image) : '',
          };
        }) || [],
      );

      return {
        ...category.toJSON(),
        subcategories: subcategoriesWithFlags,
        image: category.image ? viewImages(category.image) : '',
      };
    }),
  );

  return categoriesWithFlags;
};

export const addProductCategory = async (data: {
  name: string;
  image: string;
  points: number;
  product_flag: boolean;
  category_id: number;
}) => {
  const { name, image, category_id, points, product_flag } = data;
  const existingCategory = await PlumberCategory.findOne({ where: { name: name, parent_id: category_id ?? null } });
  if (existingCategory) {
    throw new HttpError('Category with this name already exists', 400);
  }
  const imageUrl = saveImages(image) as string;
  const category = await PlumberCategory.create({
    name: name,
    image: imageUrl,
    parent_id: category_id ?? null,
    points,
    product_flag,
  });

  return category;
};

export const addCategory = async (data: { name: string; image: string; points: number; category_id?: number }) => {
  const { name, image, category_id, points } = data;
  const existingCategory = await PlumberCategory.findOne({ where: { name: name, parent_id: category_id ?? null } });
  if (existingCategory) {
    throw new HttpError('Category with this name already exists', 400);
  }
  const imageUrl = saveImages(image) as string;
  const category = await PlumberCategory.create({
    name: name,
    image: imageUrl,
    parent_id: category_id ?? null,
    // points,
    product_flag: false,
  });

  return category;
};

export const getCategories = async () => {
  const categories = await PlumberCategory.findAll({
    where: {
      parent_id: {
        [Op.eq]: null,
      },
    },
  });

  return categories.map(category => {
    return {
      ...category.toJSON(),
      image: category.image ? viewImages(category.image) : '',
    };
  });
};

export const getCategoryById = async (id: string) => {
  const category = await PlumberCategory.findByPk(id, {
    include: [
      {
        model: PlumberCategory,
        as: 'subcategories',
      },
    ],
  });

  if (!category) throw new HttpError('category not found', 404);

  const categoryJSON = category.toJSON(); // Convert to JSON to safely modify properties

  const subcategoriesWithFlags = await Promise.all(
    categoryJSON.subcategories?.map(async item => {
      const exist = await PlumberCategory.findOne({ where: { parent_id: item.id } });
      return {
        ...item,
        has_subcategory: !!exist,
        image: item.image ? viewImages(item.image) : '',
      };
    }) || [],
  );

  return {
    ...categoryJSON,
    image: category.image ? viewImages(category.image) : '',
    subcategories: subcategoriesWithFlags,
  };
};

export const updateCategory = async (
  id: string,
  { name, image, category_id, points }: { name: string; image: string; points: number; category_id?: number },
) => {
  const category = await PlumberCategory.findByPk(id);

  if (!category) {
    throw new HttpError('Category not found', 404);
  }
  category.name = name || category.name;
  category.points = points || category.points;

  category.image = (saveImages(image) as string) || category.image;
  if (category_id) category.parent_id = category_id;

  await category.save();
  return category;
};

export const deleteCategory = async (id: string) => {
  const category = await PlumberCategory.findByPk(id);
  if (!category) {
    throw new HttpError('Category not found', 404);
  }
  await category.destroy();
  return category;
};
