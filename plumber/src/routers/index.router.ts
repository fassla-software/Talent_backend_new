import express from 'express';
import userRouter from '../modules/user/user.routes';
import plumberRouter from '../modules/plumber/plumber.routes';
import uploadRouter from '../modules/upload/upload.router';
import categoryRouter from '../modules/plumberCategory/plumber-category.routes';
import InspectionRequestRouter from '../modules/inspectionRequest/inspection-request.routes';
import InspectionVisitRouter from '../modules/inspectionVisit/inspection-visit.routes';
import contactUsRouter from '../modules/contactUs/contactus.routes';
import certificateRouter from '../modules/certificate/certificate.routes';
import giftRouter from '../modules/gift/gift.routes';
import receivedGiftRouter from '../modules/gift/receivedGift/received_gift.routes';
import withdrawRouter from '../modules/withdrawRequest/withdraw.routes';
import configRouter from '../modules/config/config.routes';
import segmentRouter from '../modules/segment/segment.routes';
import traderRouter from '../modules/trader/trader.routes';
import reportDropdownRouter from '../modules/reportDropdowns/report-dropdowns.routes';
import ticketRouter from '../modules/ticket/ticket.routes';

const indexRouter = express.Router();

indexRouter.use('/user', userRouter);
indexRouter.use('/plumber', plumberRouter);
indexRouter.use('/trader', traderRouter);
indexRouter.use('/category', categoryRouter);
indexRouter.use('/request', InspectionRequestRouter);
indexRouter.use('/inspection-visit', InspectionVisitRouter);
indexRouter.use('/upload', uploadRouter);
indexRouter.use('/contact', contactUsRouter);
indexRouter.use('/certificate', certificateRouter);
indexRouter.use('/gift', giftRouter);
indexRouter.use('/receivedGift', receivedGiftRouter);
indexRouter.use('/withdraw', withdrawRouter);
indexRouter.use('/config', configRouter);
indexRouter.use('/segment', segmentRouter);
indexRouter.use('/report-dropdowns', reportDropdownRouter);
indexRouter.use('/ticket', ticketRouter);

export default indexRouter;
