import { Request, Response } from 'express';
import { AuthenticatedRequest } from '../../@types/express';
import awardService from './award.service';

class AwardController {
    // Award CRUD
    async createAward(req: Request, res: Response) {
        try {
            const award = await awardService.createAward(req.body);
            res.status(201).json(award);
        } catch (error: any) {
            res.status(400).json({ message: error.message });
        }
    }

    async getAwards(req: Request, res: Response) {
        try {
            const awards = await awardService.getAwards();
            res.json(awards);
        } catch (error: any) {
            res.status(500).json({ message: error.message });
        }
    }

    async getAwardById(req: Request, res: Response) {
        try {
            const award = await awardService.getAwardById(Number(req.params.id));
            if (!award) return res.status(404).json({ message: 'Award not found' });
            res.json(award);
        } catch (error: any) {
            res.status(500).json({ message: error.message });
        }
    }

    async updateAward(req: Request, res: Response) {
        try {
            const award = await awardService.updateAward(Number(req.params.id), req.body);
            res.json(award);
        } catch (error: any) {
            res.status(400).json({ message: error.message });
        }
    }

    async deleteAward(req: Request, res: Response) {
        try {
            await awardService.deleteAward(Number(req.params.id));
            res.status(204).send();
        } catch (error: any) {
            res.status(400).json({ message: error.message });
        }
    }

    // Envoy Award CRUD
    async assignAwardToEnvoy(req: Request, res: Response) {
        try {
            const envoyAward = await awardService.assignAwardToEnvoy(req.body);
            res.status(201).json(envoyAward);
        } catch (error: any) {
            res.status(400).json({ message: error.message });
        }
    }

    async getEnvoyAwards(req: Request, res: Response) {
        try {
            const { page, limit, search } = req.query;
            const envoyAwards = await awardService.getEnvoyAwards({
                page: page ? Number(page) : undefined,
                limit: limit ? Number(limit) : undefined,
                search: search as string,
            });
            res.json(envoyAwards);
        } catch (error: any) {
            res.status(500).json({ message: error.message });
        }
    }

    async getEnvoyAwardById(req: Request, res: Response) {
        try {
            const envoyAward = await awardService.getEnvoyAwardById(Number(req.params.id));
            if (!envoyAward) return res.status(404).json({ message: 'Envoy Award not found' });
            res.json(envoyAward);
        } catch (error: any) {
            res.status(500).json({ message: error.message });
        }
    }

    async updateEnvoyAward(req: Request, res: Response) {
        try {
            const envoyAward = await awardService.updateEnvoyAward(Number(req.params.id), req.body);
            res.json(envoyAward);
        } catch (error: any) {
            res.status(400).json({ message: error.message });
        }
    }

    async deleteEnvoyAward(req: Request, res: Response) {
        try {
            await awardService.deleteEnvoyAward(Number(req.params.id));
            res.status(204).send();
        } catch (error: any) {
            res.status(400).json({ message: error.message });
        }
    }

    // Get Envoys
    async getEnvoys(req: Request, res: Response) {
        try {
            const envoys = await awardService.getEnvoys();
            res.json(envoys);
        } catch (error: any) {
            res.status(500).json({ message: error.message });
        }
    }

    // Get My Awards (for Envoy)
    async getMyAwards(req: Request, res: Response) {
        try {
            const authReq = req as AuthenticatedRequest;
            const envoyId = Number(authReq.user?.id);
            if (!envoyId) return res.status(401).json({ message: 'Unauthorized' });

            const awards = await awardService.getAwardsByEnvoyId(envoyId);
            res.json(awards);
        } catch (error: any) {
            res.status(500).json({ message: error.message });
        }
    }
}

export default new AwardController();
