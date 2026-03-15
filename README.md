# EternalPHP Framework

`eternalphp/framework` 是一个全栈的、轻量级的现代化 PHP Web 开发框架。它汲取了现代 PHP 框架的诸多优秀设计理念（如依赖注入容器、中间件、Pipeline、Eloquent ORM 等），同时依然保持了对 PHP 5.6+ 的兼容性，在轻量与现代化之间取得了良好的平衡。

## 系统要求

- PHP >= 5.6
- [filp/whoops](https://github.com/filp/whoops) (错误提示组件)

## 核心特性

本框架包含了一套完整的现代 Web 开发所需的组件库，主要特性包括：

- **依赖注入与容器 (Container / Foundation\Application)**：提供了强大的反向控制（IoC）容器，支持自动绑定与单例管理。
- **灵活的路由系统 (Router)**：支持 RESTful 路由、路由分组、中间件挂载等高效的路由调度机制。
- **中间件与管道 (Middleware & Pipeline)**：基于 Pipeline 实现的请求处理流，方便在请求到达控制器前后进行拦截、过滤或处理（如鉴权、日志、请求修改等）。
- **对象关系映射 (Database / Eloquent)**：内置类似于 Laravel Eloquent 的强大 ORM 实现，支持模型关联（Relation）、数据库迁移以及 Schema 结构构建器。
- **事件机制 (Event)**：支持同步事件处理和异步事件系统，解耦业务逻辑。
- **缓存与 Redis 集成 (Cache & Redis)**：统一的缓存接口支持，轻松对接多级缓存及 Redis 数据操作。
- **请求与响应封装 (Http / Session / Cookie)**：提供了统一的 Request 和 Response 抽象对象，包含对 Session 及 Cookie 的便捷管理。
- **CLI 命令行支持 (Console)**：内置命令行工具体系，方便开发定时任务和维护脚本。
- **多语言化 (Language)**：原生支持 i18n 多语言环境。
- **助手函数 (Foundation\functions.php)**：框架提供了一系列开箱即用的全局助手函数，比如：
  - `app()`, `application()`: 快速获取容器实例。
  - `config()`, `env()`: 配置信息的快速访问。
  - `request()`, `response()`, `success()`, `fail()`: 便捷的 HTTP 操作。
  - `html_in()`, `html_out()`, `sql_in()`: 内置的安全过滤机制，有效防范 XSS 及 SQL 注入。
  - `https_request()`: 全局 CURL 快捷调用方法。

## 安装说明

通过 Composer 进行安装或在项目依赖中引入：

```json
{
    "require": {
        "eternalphp/framework": "dev-master"
    }
}
```

执行：

```bash
composer install
```

## 目录结构介绍

框架的源码均位于 `src/framework` 中，主要模块如下：

```
src/framework/
├── Cache/         # 缓存服务
├── Config/        # 配置加载与管理
├── Console/       # CLI 命令行处理模块
├── Container/     # 核心 IoC 容器
├── Controller/    # 基础控制器基类
├── Cookie/        # Cookie 管理模块
├── Database/      # 数据库连接层、Schema构建器以及 Eloquent ORM 实现
├── Debug/         # 调试模式相关工具
├── Event/         # 事件调度器 (同步/异步)
├── Exception/     # 框架内部统一异常处理机制
├── Filesystem/    # 文件系统操作接口封装
├── Foundation/    # 框架核心应用生命周期模块 (如 Application.php, 核心助手函数)
├── Hashing/       # 数据哈希加密组件
├── Http/          # HTTP 请求、响应及上传处理封装
├── Language/      # 多语言本地化支持
├── Logger/        # 日志记录系统
├── Middleware/    # HTTP 中间件机制
├── Pipeline/      # 管道中间件调度实现
├── Redis/         # Redis 原生类支持
├── Router/        # 网络请求路由分发机制
├── Session/       # 会话控制服务
├── Support/       # 支持和助手类 (如 Collection、Str 辅助类)
├── Util/          # 通用工具类组件
├── Validate/      # 数据和表单校验规则引擎
└── View/          # 视图编译与渲染层
```

## 生命周期概览

1. **实例化 Application 容器**：在入口文件中初始化 `framework\Foundation\Application` 实例。
2. **初始化框架环境 (init)**：加载配置（Config）、定义基础路径、绑定核心服务。
3. **HTTP 调度**：通过 `Router` 及 `Pipeline` 获取并执行匹配到的中间件链路（Middleware）。
4. **触发 Controller**：完成中间件调度后，实例化相应的 Controller ，执行业务逻辑。
5. **返回 Response**：通过规范的 Response 发送 HTTP 报文给客户端，并在周期末尾清理容器或触发终止事件。

## 作者与协议

- **Author**: yuanzhongyi (kld230@163.com)
- **License**: 此框架在 MIT 协议下开源发行。
