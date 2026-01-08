import { RequestStatus } from '../inspection_request.model';

export interface ICreateInspectionRequest {
  user_name: string;
  user_phone: string;
  nationality_id: string;
  area: string;
  city: string;
  address: string;
  seller_name: string;
  seller_phone: string;
  items: { subcategory_id: string; count: number }[];
  certificate_id: string;
  inspection_date: string;
  description: string;
  images: string[];
  user_lat: number;
  user_long: number;
  status?: RequestStatus.PENDING | RequestStatus.SEND;
  plumber_id?: string;
}
