import { RequestFilterStatus } from './request-filter-status.dto';

export interface IFilter {
  city?: string;
  area?: string;
  user_name?: string;
  plumber_name?: string;
  status?:
    | RequestFilterStatus.ACCEPTED
    | RequestFilterStatus.REJECTED
    | RequestFilterStatus.REVIEWED
    | RequestFilterStatus.SEND
    | RequestFilterStatus.UNDER_REVIEW
    | RequestFilterStatus.PENDING;

  limit?: number;
  skip?: number;
}
