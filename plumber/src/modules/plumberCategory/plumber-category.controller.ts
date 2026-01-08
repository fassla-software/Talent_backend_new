import { Request, Response } from 'express';
import { asyncHandler } from '../../utils/asyncHandler';
import {
  getCategories,
  addCategory,
  updateCategory,
  deleteCategory,
  getCategoriesWithSubcategory,
  getCategoryById,
  getPlumberCategoryTreeDynamic,
  addProductCategory,
} from './plumber-category.service';

export const getTreeCategoriesHandler = asyncHandler(async (req: Request, res: Response) => {
  const categories = await getPlumberCategoryTreeDynamic();
  res.status(200).json({ categories });
}, 'Failed to get categories');

export const getAllCategoriesHandler = asyncHandler(async (req: Request, res: Response) => {
  const categories = await getCategoriesWithSubcategory();
  res.status(200).json({ categories });
}, 'Failed to get categories');

export const getPlumberCategoriesHandler = asyncHandler(async (req: Request, res: Response) => {
  const categories = await getCategories();
  res.status(200).json({ categories });
}, 'Failed to get categories');

export const getPlumberSubcategoryOfCategoryHandler = asyncHandler(async (req: Request, res: Response) => {
  const id = req.params.id;
  const category = await getCategoryById(id);
  res.status(200).json({ category });
}, 'Failed to get categories');

export const addCategoryPlumbersHandler = asyncHandler(async (req: Request, res: Response) => {
  const data = req.body;
  const category = await addCategory(data);
  res.status(200).json({ category });
}, 'Failed to add category');

export const addProductCategoryPlumbersHandler = asyncHandler(async (req: Request, res: Response) => {
  const data = req.body;
  const category = await addProductCategory(data);
  res.status(200).json({ category });
}, 'Failed to add category');

export const updatePlumberCategoryHandler = asyncHandler(async (req: Request, res: Response) => {
  const categoryId = req.params.id;
  const data = req.body;
  const category = await updateCategory(categoryId, data);
  res.status(200).json({ category });
}, 'Failed to update category');

export const deletePlumberCategoryHandler = asyncHandler(async (req: Request, res: Response) => {
  const categoryId = req.params.id;
  const category = await deleteCategory(categoryId);
  res.status(200).json({ category });
}, 'Failed to delete category');
