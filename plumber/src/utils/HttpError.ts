import { ValidationError } from 'express-validator';

class HttpError extends Error {
  statusCode: number;
  isValidationError: boolean = false;
  error: any; // eslint-disable-line @typescript-eslint/no-explicit-any

  setAsValidationError() {
    this.isValidationError = true;
  }

  constructor(message: string, statusCode: number, err?: Error | ValidationError[]) {
    super(message);
    this.statusCode = statusCode;
    this.error = err;
    Error.captureStackTrace(this, this.constructor);
  }

  log(errorLogStream: NodeJS.WriteStream) {
    errorLogStream.write(
      `Time: ${new Date().toLocaleString()} Error Message: ${this.message} Error: ${this.error} Error Stack:${
        this.stack
      }\n`,
    );
  }
}

export default HttpError;
