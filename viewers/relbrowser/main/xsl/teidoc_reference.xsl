<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:str="http://exslt.org/strings" xmlns:xi="http://www.w3.org/2001/XInclude" version="1.0">

	<xsl:param name="flavour"/>

	<xsl:include href="clean_quote.xsl"/>
	<xsl:template
		match="related[rectype=99] | pointer[rectype/@id=99] | reverse-pointer[rectype/@id=99]">
		<xsl:param name="matches"/>
		<xsl:choose>
			<xsl:when test="$matches">

				<xsl:call-template name="setup_refs"/>

				<xsl:apply-templates select="$matches">
					<xsl:sort data-type="number" select="detail[@id=341]"/>
					<xsl:sort data-type="number" select="id"/>
				</xsl:apply-templates>

			</xsl:when>
			<xsl:otherwise>
				<div class="relatedItem">
						<!--a href="{$base}/item/{id}/{/export/references/reference/rectype/@id}?flavour={$flavour}#ref1"-->

						<!-- chose if base id = annotation target id - then include onclick handler and href is below -->
						<div class="editIcon">
						<a href="{$appBase}edit-annotation.html?id={id}?instance={$instanceName}"
							onclick="window.open(this.href,'','status=0,scrollbars=1,resizable=1,width=800,height=600'); return false;"
							title="edit">
							<img src="{$hBase}common/images/edit-pencil.png"/>
						</a>
						</div>
						<div class="annotation">
						<xsl:choose>
							<xsl:when test="../id = pointer[@id=322]/id">
								<a href="#ref{id}" annotation-id="{id}"
									onclick="showFootnote({id}); highlightAnnotation({id});"
									class="sb_two">
									<xsl:value-of select="title"/>
								</a>
							</xsl:when>
							<xsl:otherwise>
								<a href="{$cocoonBase}item/{pointer[@id=322]/id}/#ref{id}?instance={$instanceName}" class="sb_two">
									<xsl:value-of select="title"/>
								</a>
								<span>(annotation in: <em>
									<xsl:value-of select="pointer[@id=322]/title"/>
								</em>
								<img style="vertical-align: middle;horizontal-align: right"
									src="{$hBase}common/images/rectype-icons/{pointer[@id=322]/rectype/@id}.png"/>)
								</span>
							</xsl:otherwise>
						</xsl:choose>
                        </div>
						<!-- iotherwise get the id if the target and -->
				</div>

				<xsl:call-template name="add_ref">
					<xsl:with-param name="ref" select="."/>
				</xsl:call-template>

			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="reverse-pointer[rectype/@id=99]" mode="footnote">
		<div name="footnote" recordID="{id}">

			<div name="footnotesleft">
						<p><b><xsl:value-of select="detail[@id=160]/text()"/></b>
							
						<xsl:call-template name="paragraphise">
							<xsl:with-param name="text">
								<xsl:value-of select="detail[@id=191]/text()"/>
							</xsl:with-param>
						</xsl:call-template>
						</p>	
			</div>
			<div name="footnotesright">
						<p>
							<xsl:choose>
								<xsl:when test="pointer[@id=152]/detail/file_thumb_url">
									<a href="{$cocoonBase}item/{pointer[@id=152]/id}/?instance={$instanceName}">
										<img src="{pointer[@id=152]/detail/file_thumb_url}"
											border="0"/>
									</a>
								</xsl:when>
								<xsl:otherwise>
									<table cellpadding="5">
										<tr>
											<td>

												<a href="{$cocoonBase}item/{pointer[@id=152]/id}/?instance={$instanceName}"
												class="sb_two">
												<xsl:value-of
												select="pointer[@id=152]/detail[@id=291]"/>
												<xsl:text> </xsl:text>
												<xsl:value-of
												select="pointer[@id=152]/detail[@id=160]"/>
												</a>
											</td>
											<td>

												<!-- change this to pick up the actuall system name of the rectype or to use the mapping method as in JHSB that calls human-readable-names.xml -->
												<img
												style="vertical-align: middle;horizontal-align: right"
												src="{$hBase}common/images/rectype-icons/{pointer[@id=152]/rectype/@id}.png"
												/>
											</td>
										</tr>
									</table>

								</xsl:otherwise>
							</xsl:choose>

						</p>
			</div>

			<div name="xpath">
				<small> (xpath: /TEI/text/body/div[]/p[] <xsl:value-of select="detail[@id=539]"/>)</small>
			</div>
		</div>
	</xsl:template>


	<xsl:template match="reference[rectype/@id=99]">
<xsl:comment>reference template</xsl:comment>
		<script src="{$appBase}js/highlight.js"/>
		<style>
			#tei a { text-decoration: none; }
		</style>

		<!--xsl:if test="pointer[@id=322]/detail[@id=221]">
			<div id="tei">
				<xi:include href="{pointer[@id=322]/detail[@id=221]/file_fetch_url}"/>
			</div>
			<xsl:call-template name="setup_refs"/>
			<xsl:call-template name="add_ref">
				<xsl:with-param name="ref" select="."/>
			</xsl:call-template>
			<xsl:call-template name="render_refs"/>
		</xsl:if-->

		<table>
			<xsl:for-each select="detail[@id!=222 and @id!=223 and @id!=224 and @id!=230]">
				<tr>
					<td style="padding-right: 10px;">
						<nobr>
							<xsl:choose>
								<xsl:when test="string-length(@name)">
									<xsl:value-of select="@name"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="@type"/>
								</xsl:otherwise>
							</xsl:choose>
						</nobr>
					</td>
					<td>
						<xsl:choose>
							<xsl:when test="@id=191">
								<xsl:call-template name="paragraphise">
									<xsl:with-param name="text">
										<xsl:value-of select="text()"/>
									</xsl:with-param>
								</xsl:call-template>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="text()"/>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
			</xsl:for-each>
		</table>

	</xsl:template>

	<xsl:template name="setup_refs">
		<script>
			if (! window["refs"]) {
				window["refs"] = [];
			}
		</script>
	</xsl:template>

	<xsl:template name="add_ref">
		<xsl:param name="ref"/>
		<script>
			if (refs) {
				refs.push( {
				startElems : [ <xsl:value-of select="detail[@id=539]"/> ],
				endElems : [ <xsl:value-of select="detail[@id=540]"/> ],
				startWord :
					<xsl:choose>
						<xsl:when test="detail[@id=329]"><xsl:value-of select="detail[@id=329]"/></xsl:when>
						<xsl:otherwise>null</xsl:otherwise>
					</xsl:choose>,
				endWord :
					<xsl:choose>
						<xsl:when test="detail[@id=330]"><xsl:value-of select="detail[@id=330]"/></xsl:when>
						<xsl:otherwise>null</xsl:otherwise>
					</xsl:choose>,
				href : "../<xsl:value-of select="id"/>/?flavour=<xsl:value-of select="$flavour"/>#ref1",
				title : "<xsl:call-template name="cleanQuote"><xsl:with-param name="string" select="detail[@id=160]"/></xsl:call-template>",
				recordID : "<xsl:value-of select="id"/>"
				} );
			}
		</script>
	</xsl:template>

	<xsl:template name="render_refs">
		<script>
		<![CDATA[
			var root = document.getElementById("tei");
			if (root  &&  window["refs"])
			highlight(root, refs);
			window.onload = highlightOnLoad;
		]]>
		</script>
	</xsl:template>

</xsl:stylesheet>
