local postIn, postKey, postValue, getIn, tmp, switch, itemID, itemValue, location, args, erreur, ok, err, categories, citation, 
	res, rows, resp, flags, credit, red, compteur, parser, template, postgresql, redis, starttime = 
	"", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", 0, require "rds.parser", require "resty.template", 1, require "resty.redis", os.clock()

-- agentzh's lua-resty-mysql module : top
-- agentzh's lua-resty-mysql module : bot

local function escapesinglequote(str) return string.gsub(str, "'", "\'") end
local function nline2br(str) return string.gsub(str, "\n", "<br />") end
local function quotetosingle(str) return string.gsub(str, '"', "'") end
local function trim(str) return str:gsub("^%s*(.-)%s*$", "%1") end
local function quote(str) return '"' .. str .. '"' end

local function vignette_template(tparam1, tparam2, tparam3)

	local timagenum, twidth = math.random(8), math.random(100) + 270
	return "<article class=" .. quote("item thumb") .. " data-width=" .. quote(twidth) .. " data-value=" .. quote(quotetosingle(tparam2) .. 
		"</br >" .. tparam1) .. "><div class=" .. quote("citation") .. " style=" .. quote("background-image: url(images/thumbs/0" .. 
		timagenum .. ".jpg)") .. ">" .. quotetosingle(tparam2) .. "</div><h7><a href=" .. quote("#") .. " onclick=" .. quote("javascript:$('.image').click();") .. 
		" class=" .. quote("icon fa-arrows-alt") .. "><span class=" .. quote("label") .. ">Detail</span></a></h7>" .. "<h2><input class=" .. 
		quote("ccategories") .. " type=" .. quote("submit") .. " name=" .. quote("search") .. " value=" .. quote(tparam1) .. "></h2><a href=" 
		.. quote("images/fulls/0" .. timagenum .. ".jpg") .. " class=" .. quote("image") .. " alt=" .. quote("") .. "><img src=" .. 
		quote("images/thumbs/0" .. timagenum .. ".jpg") .. " alt=" .. quote("") .. "></a></article>\r\n"
		
end

-- redis conection initer : top

red, err = redis:new()
if not red then ngx.say(err) end
red:set_timeout(1000) -- 1 sec
ok, err = red:connect("127.0.0.1", "6379")
if not ok then ngx.say(err) end

-- redis connection initer : bot

-- business treatments : top

if ngx.var.request_method == "POST" then

	-- POST's tasks controler
			
	ngx.req.read_body()
	args, err = ngx.req.get_post_args(20)
	if err 
	then erreur = err
	else
	
		-- tasks distribution listener : top
		
		for key, val in pairs(args) do
			if type(val) == "table" then
			
				-- postIn = postIn .. key .. "=" .. table.concat(val, ", ") .. "&" -- unactive
				-- ngx.var.concept = table.concat(val, ", ") -- unactive
				
			else
			
				postIn = postIn .. key .. "=" .. val .. "&"
				-- split
				postKey		= key
				postValue	= val
				ngx.var.concept = val
				
			end
		end
		
		-- tasks distribution listener : bot
		
	end
	
	-- POST's first of n possible businesses treatments
	
	if string.sub(postIn, 1, 7) == "search=" then
		
		if #postValue < 5 and tonumber(postValue) == nil
		then erreur = "Critère de recherche inadéquat !"
		else
		
			res, flags, err = red:get("citalis_citations_search_search_" .. tostring(postValue)) -- redis 1
			if err or not res or res == ngx.null then
		
				if postgresql == 1
				then resp, err = ngx.location.capture("/citalis.citations.search?search=" .. escapesinglequote(tostring(postValue)))
				else end
				if err 
				then erreur = err
				else
					res, err = parser.parse(resp.body);
					if err 
					then erreur = err
					else
						rows, err = res.resultset
						if err 
						then erreur = err
						else
							
							for i, row in ipairs(rows) do
								citation = ""
								for col, val in pairs(row) do
									if col == "concept"
									then tmp = val
									elseif trim(tostring(val)) ~= ""
									then citation = tostring(val) .. "<br /><br />" .. citation end
								end
								compteur = compteur + 1
								categories = categories .. vignette_template(postValue, citation .. tmp, compteur) 
							end
				
							ok, err = red:set("citalis_citations_search_search_" .. tostring(postValue), categories)
							if not ok then erreur = err end
				
						end
					end
				end
						
			else
			
				categories = tostring(res)
				compteur = select(2, categories:gsub('\n', '\n'))
				credit = "c "
				
			end
			
		end
	
	elseif string.sub(postIn, 1, -2) == "auteur=extract" or
		string.sub(postIn, 1, -2) == "date=extract" then -- no redis 2
		
		if postgresql == 1
		then resp, err = ngx.location.capture("/citalis." .. tostring(postKey) .. "s")
		else end
		if err 
		then erreur = err
		else
			res, err = parser.parse(resp.body);
			if err 
			then erreur = err
			else
				rows, err = res.resultset
				if err 
				then erreur = err
				else
				
					for i, row in ipairs(rows) do
						for col, val in pairs(row) do
					
							-- hors sortie json/RDS, "\n" de séparateur des champs pg --> "<br />" : top --
							
							if string.sub(postIn, 1, -2) == "auteur=extract"
							then resp = ngx.location.capture("/citalis.citations." .. postKey .. ".text?auteur=" .. val)
							else resp = ngx.location.capture("/citalis.citations." .. postKey .. ".text.extract?date=" .. val) end
							
							compteur = compteur + 1
							categories = categories .. vignette_template(tostring(val), nline2br(resp.body), compteur)
							
							-- hors sortie json/RDS, "\n" de séparateur des champs pg --> "<br />" : bot --
							-- option texte produisant 100% de retour sans erreur du driver ngx.postgresql --
				
						end
					end
				end
				
			end
		end

	elseif string.sub(postIn, 1, -2) == "concept=random" or 
		string.sub(postIn, 1, -2) == "auteur=random" or 
		string.sub(postIn, 1, -2) == "date=random" then
			
		if postKey == "concept" and postgresql == 1
		then postValue, err = ngx.location.capture("/citalis.concepts.random").body
		elseif postKey == "auteur" and postgresql == 1
		then postValue, err = ngx.location.capture("/citalis.auteurs.random").body
		elseif postKey == "date" and postgresql == 1
		then postValue, err = ngx.location.capture("/citalis.dates.random").body 
		else end
		if err 
		then erreur = err 
		else
		
			if postgresql == 1 and postKey == "concept" then
			
				res, flags, err = red:get("citalis_citations_" .. tostring(postkey) .. "_" .. tostring(postValue)) -- redis 3
				if err or not res or res == ngx.null then
					resp, err = ngx.location.capture("/citalis.citations?" .. postKey .. "=" .. postValue).body
					
					ok, err = red:set("citalis_citations_" .. tostring(postkey) .. "_" .. tostring(postValue), resp)
					if not ok then erreur = err end
					
				else
				
					resp = tostring(res)
					credit = "c "
					
				end
				
			elseif postgresql == 1 and postKey == "auteur" then
			
				res, flags, err = red:get("citalis_citations_auteur_" .. tostring(postkey) .. "_" .. tostring(postValue)) -- redis 4
				if err or not res or res == ngx.null then
					resp, err = ngx.location.capture("/citalis.citations.auteur?" .. postKey .. "=" .. postValue).body
					
					ok, err = red:set("citalis_citations_auteur_" .. tostring(postkey) .. "_" .. tostring(postValue), resp)
					if not ok then erreur = err end
					
				else
				
					resp = tostring(res)
					credit = "c "
				
				end
				
			elseif postgresql == 1 and postKey == "date" then
			
				res, flags, err = red:get("citalis_citations_date_" .. tostring(postkey) .. "_" .. tostring(postValue)) -- redis 5
				if err or not res or res == ngx.null then
					resp, err = ngx.location.capture("/citalis.citations.date?" .. postKey .. "=" .. postValue).body
					
					ok, err = red:set("citalis_citations_date_" .. tostring(postkey) .. "_" .. tostring(postValue), resp)
					if not ok then erreur = err end
					
				else
				
					resp = tostring(res)
					credit = "c "
				
				end
				
			else end
				
			if err
			then erreur = err
			else
			
				res, err = parser.parse(resp)
				if err 
				then erreur = err
				else
					rows, err = res.resultset
					if err 
					then erreur = err
					else
					
						for i, row in ipairs(rows) do
							citation = ""
							for col, val in pairs(row) do
					
								if postKey == "concept"
								then citation = tostring(val) .. "<br />" .. citation
								elseif postKey == "auteur" then
									if col == "concept" 
									then postValue = val
									else citation = tostring(val) .. "<br />" .. citation end
								elseif postKey == "date" then
									if col == "date" 
									then postValue = val end
									citation = tostring(val) .. "<br />" .. citation
								end
						
							end
							compteur = compteur + 1
							categories = categories .. vignette_template(postValue, citation, compteur) 
						end
						
					end
				end
			end
		
		end
		
	elseif string.sub(postIn, 1, -2) == "home=random" then -- no redis 6
	
		if postgresql == 1
		then resp, err = ngx.location.capture("/citalis.concepts")
		else end
		if err 
		then erreur = err
		else
			res, err = parser.parse(resp.body);
			if err 
			then erreur = err
			else
				rows, err = res.resultset
				if err 
				then erreur = err
				else
					for i, row in ipairs(rows) do
						for col, val in pairs(row) do
							
							-- hors sortie json/RDS, "\n" de séparateur des champs pg --> "<br />" : top --
							
							resp = ngx.location.capture("/citalis.citations.random.text?concept=" .. val)
							compteur = compteur + 1
							categories = categories .. vignette_template(tostring(val), nline2br(resp.body), compteur)
							
							-- hors sortie json/RDS, "\n" de séparateur des champs pg --> "<br />" : bot --
							-- option texte produisant 100% de retour sans erreur du driver ngx.postgresql --
						
						end
					end
					
					ok, err = red:set("citalis_concepts", categories) -- redis 6
					if not ok then erreur = err end
					
				end
			end
		end
	
	else
		
		res, flags, err = red:get("citalis_citations_concept_" .. tostring(ngx.var.concept)) -- redis 7
		if err or not res or res == ngx.null then
		
			if postgresql == 1
			then resp, err = ngx.location.capture("/citalis.citations?concept=" .. tostring(ngx.var.concept))
			else end
			if err 
			then erreur = err
			else
				res,err = parser.parse(resp.body) 
				if err 
				then erreur = err
				else
					rows, err = res.resultset; 
					if err 
					then erreur = err
					else
					
						for i, row in ipairs(rows) do
							citation = ""
							for col, val in pairs(row) do
								citation = tostring(val) .. "<br />" .. citation
							end
							compteur = compteur + 1
							categories = categories .. vignette_template(tostring(ngx.var.concept), citation, compteur)
						end
					
						ok, err = red:set("citalis_citations_concept_" .. tostring(ngx.var.concept), categories)
						if not ok then erreur = err end
					
					end
				end
			end
			
		else
		
			categories = tostring(res)
			compteur = select(2, categories:gsub('\n', '\n'))
			credit = "c "
		
		end
	
	end
	
elseif ngx.var.request_method == "GET" then

	-- GET's tasks responder
	
	if ngx.var.concept .. ngx.var.auteur .. ngx.var.date == "" then
	
		-- app initer
		
		res, flags, err = red:get("citalis_concepts") -- redis 8
		-- res, err = ngx.location.capture("/redis.get?key=citalis_concepts") -- lent
		if err or not res or res == ngx.null then
		
			if postgresql == 1
			then resp, err = ngx.location.capture("/citalis.concepts")
			else end
			if err 
			then erreur = err
			else
				res, err = parser.parse(resp.body);
				if err 
				then erreur = err
				else
					rows, err = res.resultset
					if err 
					then erreur = err
					else
						for i, row in ipairs(rows) do
							for col, val in pairs(row) do
											
								-- hors sortie json/RDS, "\n" de séparateur des champs pg --> "<br />" : top --
								
								resp = ngx.location.capture("/citalis.citations.random.text?concept=" .. val)
								compteur = compteur + 1
								categories = categories .. vignette_template(tostring(val), nline2br(resp.body), compteur)
								
								-- hors sortie json/RDS, "\n" de séparateur des champs pg --> "<br />" : bot --
								-- option texte produisant 100% de retour sans erreur du driver ngx.postgresql --
						
							end
						end
								
						-- ok, err = ngx.location.capture("/redis.set?key=citalis_concepts&value=" .. categories) -- lent
						ok, err = red:set("citalis_concepts", categories)
						if not ok then erreur = err end
			
					end
				end
			end
			
		else
		
			categories = tostring(res) -- categories = tostring(res.body) -- lent
			compteur = select(2, categories:gsub('\n', '\n'))
			credit = "c "
		
		end
		
	end 
	
else return ngx.redirect("/40x.html") end

-- redis connection pool : top --

-- size 100, with 10 seconds max idle timeout
ok, err = red:set_keepalive(10000, 100)
if not ok then ngx.say("failed to set keepalive: ", err) end

-- redis connection pool : bot --	

-- business treatments : bot

-- web browser output : top

if erreur ~= "" 
then template.render("50x.html", { message1 = erreur })
else

	if compteur == 0 then compteur = "0 sentence"
	elseif compteur < 2 then compteur = compteur .. " sentence"
	else compteur = compteur .. " sentences" end
	
	local message1, message2, message3, message4
	
	template.render("citalis_orc.html", 
		{ message1 = credit .. string.sub(os.clock() - starttime, 1, 5) .. " s", message2 = "", message3 = compteur, message4 = categories })

end

-- web browser output : bot

