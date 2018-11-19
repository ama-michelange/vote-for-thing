export class ApiModel<T> {
   data: T;
   meta?: ApiMetaModel;
}
export class ApiModels<T> {
   data: T[];
   meta?: ApiMetaModel;
}

export class ApiMetaModel {
   available_includes: string[];
   default_includes: string[];
}
