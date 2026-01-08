import { Request, Response } from 'express';
import ReportDropdownService from './report-dropdowns.service';

class ReportDropdownController {
  async getDropdownOptions(req: Request, res: Response) {
    try {
      const { type } = req.params;
      const options = await ReportDropdownService.getDropdownOptions(type);
      res.json({ success: true, data: options });
    } catch (error: any) {
      res.status(500).json({ success: false, message: error.message });
    }
  }

  async getAllDropdownTypes(req: Request, res: Response) {
    try {
      const types = await ReportDropdownService.getAllDropdownTypes();
      res.json({ success: true, data: types });
    } catch (error: any) {
      res.status(500).json({ success: false, message: error.message });
    }
  }

  async createOption(req: Request, res: Response) {
    try {
      const option = await ReportDropdownService.createOption(req.body);
      res.status(201).json({ success: true, data: option });
    } catch (error: any) {
      res.status(400).json({ success: false, message: error.message });
    }
  }

  async updateOption(req: Request, res: Response) {
    try {
      const { id } = req.params;
      const option = await ReportDropdownService.updateOption(Number(id), req.body);
      res.json({ success: true, data: option });
    } catch (error: any) {
      res.status(400).json({ success: false, message: error.message });
    }
  }

  async deleteOption(req: Request, res: Response) {
    try {
      const { id } = req.params;
      await ReportDropdownService.deleteOption(Number(id));
      res.json({ success: true, message: 'Option deleted successfully' });
    } catch (error: any) {
      res.status(400).json({ success: false, message: error.message });
    }
  }
}

export default new ReportDropdownController();