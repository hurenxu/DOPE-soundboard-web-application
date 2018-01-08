#include <stdio.h>
#include "apr_hash.h"
#include "ap_config.h"
#include "ap_provider.h"
#include "httpd.h"
#include "http_core.h"
#include "http_config.h"
#include "http_log.h"
#include "http_protocol.h"
#include "http_request.h"

#define REMOVE 1
#define REPLACE 2
#define PRESERVE 3

#define RANDOM_SIZE 5
const char* randomServer[] = {"apache", "ngix", "YuFFF", "NodeJS", "IIS"};

// Configuration structure
typedef struct
{
  char context[256];
  char replacement[256];
  char isRandomize;
  int headerStyle;
} mask_config;

static int mask_handler(request_rec *r);
const char *set_server_header(cmd_parms *cmd, void *cfg, const char *arg);
const char *set_header_replacement(cmd_parms *cmd, void *cfg, const char *arg);
const char *set_randomize(cmd_parms *cmd, void *cfg, const char *arg1);
void *create_dir_conf(apr_pool_t *pool, char *context);
void *merge_dir_conf(apr_pool_t *pool, void *BASE, void *ADD);
static void register_hooks(apr_pool_t *pool);

// Configuration directives
static const command_rec directives[] =
    {
        AP_INIT_TAKE1("ServerHeader", set_server_header, NULL, ACCESS_CONF, "How we deal server header."),
        AP_INIT_TAKE1("HeaderReplacement", set_header_replacement, 
                      NULL, ACCESS_CONF, "What value replace header"),
        AP_INIT_TAKE1("Randomize", set_randomize, NULL, ACCESS_CONF, "If random generate server header"),
        {NULL}};


module AP_MODULE_DECLARE_DATA mask_module =
    {
        STANDARD20_MODULE_STUFF,
        create_dir_conf, /* Per-directory configuration handler */
        NULL,            /* Merge handler for per-directory configurations */
        NULL,            /* Per-server configuration handler */
        NULL,            /* Merge handler for per-server configurations */
        directives,      /* Any directives we may have for httpd */
        register_hooks   /* Our hook registering function */
};


static void register_hooks(apr_pool_t *pool)
{
  ap_hook_handler(mask_handler, NULL, NULL, APR_HOOK_REALLY_FIRST);
}


static int mask_handler(request_rec *r)
{
  mask_config *config = (mask_config *)ap_get_module_config(r->per_dir_config, &mask_module);

  if (config->headerStyle == PRESERVE) {
    return DECLINED;    
  }

  char *new_server_signature = NULL;
  char *server_version = (char *)ap_get_server_banner();

  if (config->headerStyle == REMOVE) {
    new_server_signature = "";
    strcpy(server_version, new_server_signature);
    return DECLINED;
  }

  if (config->headerStyle == REPLACE) {
    if (config->isRandomize) {
      int random = rand() % RANDOM_SIZE;
      new_server_signature = randomServer[random];
    } else {
      new_server_signature = config->replacement;
    }

    strcpy(server_version, new_server_signature);

    return DECLINED;
  }

  return DECLINED;
}


const char *set_server_header(cmd_parms *cmd, void *cfg, const char *arg)
{
  mask_config *conf = (mask_config *)cfg;

  if (conf)
  {
    if (!strcasecmp(arg, "remove"))
      conf->headerStyle = REMOVE;
    else if (!strcasecmp(arg, "replace")) {
      conf->headerStyle = REPLACE;      
    } else if (!strcasecmp(arg, "preserve")) {
      conf->headerStyle = PRESERVE;      
    } else {
      //TODO error log
    }
  }

  return NULL;
}


const char *set_header_replacement(cmd_parms *cmd, void *cfg, const char *arg)
{
  mask_config *conf = (mask_config *)cfg;

  if (conf)
  {
    strcpy(conf->replacement, arg);
  }

  return NULL;
}


const char *set_randomize(cmd_parms *cmd, void *cfg, const char *arg1)
{
  mask_config *conf = (mask_config *)cfg;

  if (conf)
  {
    {
      if (!strcasecmp(arg1, "true")) {
        conf->isRandomize = TRUE;
      } else if (!strcasecmp(arg1, "false")) {
        conf->isRandomize = FALSE;
      } else {
        //TODO error
      }
    }
  }

  return NULL;
}


void *create_dir_conf(apr_pool_t *pool, char *context)
{
  context = context ? context : "Newly created configuration";

  /*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
  mask_config *cfg = apr_pcalloc(pool, sizeof(mask_config));
  /*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

  if (cfg)
  {
    {
      /* Set some default values */
      strcpy(cfg->context, context);
      cfg->headerStyle = PRESERVE;
      memset(cfg->replacement, 0, 256);
      cfg->isRandomize = FALSE;
    }
  }

  return cfg;
}